<!DOCTYPE html>
<head>
<style type="text/css">

</style>
</head>
<body>
<div>

<?php require 'backlink.php'; ?>
<pre>
SELECT COLUMN_NAME
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = 'my_database' AND TABLE_NAME = 'my_table';
</pre>
</div>

</body>
</html>
