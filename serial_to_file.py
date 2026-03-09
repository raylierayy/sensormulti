import serial
import serial.tools.list_ports
import time
import sys

import os

# Write data or errors to the file
def write_status(msg):
    try:
        filepath = os.path.join(os.path.dirname(os.path.abspath(__file__)), "sensor.txt")
        with open(filepath, "w") as f:
            f.write(msg)
    except Exception as e:
        print(f"File write error: {e}")

write_status("Connecting...")

# Step 1: Check what ports are actually available
ports = list(serial.tools.list_ports.comports())
if len(ports) == 0:
    error_msg = "ERROR: No COM ports found! Is the Arduino plugged in?"
    print(error_msg)
    write_status(error_msg)
    sys.exit(1)

# Step 2: Try to connect to COM3 (or finding the specific arduino)
target_port = 'COM3'
ser = None

try:
    ser = serial.Serial(target_port, 9600, timeout=1)
    time.sleep(2) # Give Arduino time to reset
    print(f"Successfully connected to {target_port}")
except Exception as e:
    # If COM3 fails, let the user know what ports are actually available so they can change the Python code
    available_ports = ", ".join([p.device for p in ports])
    error_msg = f"ERROR: Failed to open {target_port}. Available ports: {available_ports}"
    print(error_msg)
    write_status(error_msg)
    sys.exit(1)

# Step 3: Continuously read and update the file
try:
    while True:
        if ser.in_waiting > 0:
            data = ser.readline().decode('utf-8').strip()
            if data:
                write_status(data)
                print(f"Read: {data}")
        time.sleep(0.1) # Prevent maxing out CPU
except KeyboardInterrupt:
    write_status("ERROR: Python script was stopped manually.")
    print("\nScript stopped by user.")
except Exception as e:
    error_msg = f"ERROR: Connection lost during read: {str(e)}"
    print(error_msg)
    write_status(error_msg)
finally:
    if ser and ser.is_open:
        ser.close()
