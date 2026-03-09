#define TRIG_PIN 19
#define ECHO_PIN 18

void setup() {
  Serial.begin(9600);
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
}

void loop() {
  long duration;
  int distance;
  
  // Clear the trigPin
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  
  // Set the trigPin on HIGH state for 10 micro seconds
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);
  
  // Read the echoPin, returns the sound wave travel time in microseconds
  duration = pulseIn(ECHO_PIN, HIGH);
  
  // Calculate the distance in cm
  distance = duration * 0.034 / 2;
  
  // Send data strictly in the format that the website's javascript expects: "M=value"
  // The 'M' stands for 'Main', which matches our PHP configuration
  if(distance > 0 && distance < 400) {
      Serial.print("M=");
      Serial.println(distance);
  }
  
  delay(200); // Poll every 200 ms
}
