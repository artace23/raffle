<?php 

include "connect.php";

require_once 'Classes/PHPExcel/IOFactory.php';

    if($_FILES['excel_file']['error'] == UPLOAD_ERR_OK){ // Check if there's no upload error
        $fileInfo = pathinfo($_FILES['excel_file']['name']);
        
        $spreadsheet = PHPExcel_IOFactory::load($_FILES['excel_file']['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();  
        $worksheet_arr = $worksheet->toArray(); 

        unset($worksheet_arr[0]); 

        foreach($worksheet_arr as $row){ 
            $name = $row[0]; 
            $department = $row[1]; 

            $insertQuery = "INSERT INTO employee(name, department) VALUES(?,?)";

            $stmt1 = $conn->prepare($insertQuery);
            $stmt1->bind_param('ss', $name, $department);

            $stmt1->execute();
        }
    } else {
        echo "<script>alert('Error uploading file.')</script>";
    }

?>