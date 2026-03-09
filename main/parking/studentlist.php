<?php
// Sample data array (replace with database query in real use)
$students = [
  ["id" => "001", "name" => "Juan Dela Cruz", "date" => "2025-10-01"],
  ["id" => "002", "name" => "Maria Santos", "date" => "2025-10-05"],
  ["id" => "003", "name" => "Jose Rizal", "date" => "2025-10-10"]
];
?>

<h3>Student List</h3>
<table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse;">
  <thead style="background-color: #1e3a8a; color: white;">
    <tr>
      <th>ID Number</th>
      <th>Name</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($students as $student): ?>
      <tr>
        <td><?php echo htmlspecialchars($student["id"]); ?></td>
        <td><?php echo htmlspecialchars($student["name"]); ?></td>
        <td><?php echo htmlspecialchars($student["date"]); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>