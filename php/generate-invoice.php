<?php
require('../fpdf/fpdf.php');
include_once('../db/dbinit.php');
session_start();

// Create a new PDF instance
$pdf = new FPDF();

// Add a new page
$pdf->AddPage();
$pdf->Image('../images/logo.jpeg', ($pdf->GetPageWidth() / 2) - 15, 10, 30);
$pdf->Ln(35);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,0,'Snoopy\'s Pet Supplies',0,0,'C');
$pdf->Ln(7);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,0,'Neverland, Green Blvd, N2P 2N7',0,0,'C');
$pdf->Ln(6);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,0,'contactus@snoopy.com, #999-949-3949',0,0,'C');
$pdf->Ln(20);

$stmt = mysqli_prepare($dbc, 'SELECT first_name, last_name FROM users WHERE email = ?');
mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['first_name'] . ' ' . $row['last_name'];
} else {
    $name = 'Unknown';
}

$date = date('d-M-Y');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'Name:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(130,10,$name);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(12,10,'Date:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,$date);
$pdf->Ln(7);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'Email ID:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,10,$_SESSION['email']);

$pdf->Ln(15);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'Order ID:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,10,10101);
$pdf->Ln(10);


$stmt = mysqli_prepare($dbc, 'SELECT p.price, p.name, o.quantity
     FROM products p JOIN orders o ON o.prod_id = p.id WHERE email = ? and o.status = 1');
mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(10,10,'#',1,0,'C');
$pdf->Cell(60,10,'Product',1,0,'C');
$pdf->Cell(20,10,'Quantity',1,0,'C');
$pdf->Cell(20,10,'Price',1,0,'C');
$pdf->Cell(20,10,'Total',1,1,'C');

$pdf->SetFont('Arial','',12);

$count = 1;
$total = 0;
while($product = mysqli_fetch_assoc($result)) {
  $pdf->Cell(10,10,$count,1,0,'C');
  $pdf->Cell(60,10,$product['name'],1,0);
  $pdf->Cell(20,10,$product['quantity'],1,0,'C');
  $pdf->Cell(20,10,$product['price'],1,0,'C');
  $pdf->Cell(20,10,$product['quantity'] * $product['price'],1,1,'C');
  $total += $product['quantity'] * $product['price'];
  $count++;
}

$pdf->Cell(70);
$pdf->Cell(40,10,'Total:',1,0,'R');
$pdf->Cell(20,10,$total,1,1,'C');
$pdf->Cell(70);
$pdf->Cell(40,10,'Delivery:',1,0,'R');
$pdf->Cell(20,10,"$10",1,1,'C');
$pdf->Cell(70);
$pdf->Cell(40,10,'Tax:',1,0,'R');
$pdf->Cell(20,10, "$".number_format($total*0.15, 2),1,1,'C');
$pdf->Cell(70);
$pdf->Cell(40,10,'Grand Total:',1,0,'R');
$pdf->Cell(20,10, "$".(number_format($total*0.15,2) + $total + 10),1,1,'C');
$pdf->Ln(20);

$pdf->AliasNbPages();
$pdf->SetFont('Arial','I',8); // Set the font to Arial, italic, size 8
$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}',0,0,'C'); // Add the page number

$query = "DELETE FROM ORDERS WHERE EMAIL = ? AND STATUS = 1";
$stmt = mysqli_prepare($dbc, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
mysqli_stmt_execute($stmt);

// Output the PDF
$pdf->Output();
