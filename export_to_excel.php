<?php
include 'connection.php';
// Include PHPExcel library
require_once 'PHPExcel\.php-cs-fixer.dist.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("Your Name")
                             ->setLastModifiedBy("Your Name")
                             ->setTitle("Transaction Data")
                             ->setSubject("Transaction Data")
                             ->setDescription("Transaction data exported from the system")
                             ->setKeywords("transaction data")
                             ->setCategory("Data Export");

// Add data to the Excel file
// For example, you can add the data fetched from the database here

// Set active sheet index to the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="transaction_data.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
