<?php
require('fpdf.php'); // Assurez-vous que ce fichier est inclus une seule fois

class PDF_Custom extends FPDF
{
    private $system_name;
    private $contact;
    private $address;
    private $system_nif;

    function __construct($system_name, $contact, $address, $system_nif)
    {
        parent::__construct();
        $this->system_name = $system_name;
        $this->contact = $contact;
        $this->address = $address;
        $this->system_nif = $system_nif;
    }

    // En-tête
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->system_name, 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Contact: ' . $this->contact, 0, 1, 'C');
        $this->Cell(0, 10, 'Adresse: ' . $this->address, 0, 1, 'C');
        $this->Cell(0, 10, 'NIF: ' . $this->system_nif, 0, 1, 'C');
        $this->Ln(10);
    }

    // Pied de page
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}
?>
