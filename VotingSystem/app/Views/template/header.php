<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSPC E-Vote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Add before your existing head content -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.4/css/fixedColumns.dataTables.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>
    
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Poppins', 'sans-serif'],
            },
          }
        }
      }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen relative">