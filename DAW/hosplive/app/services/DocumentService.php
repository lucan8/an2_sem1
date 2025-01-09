<?php
    class DocumentService {
        //TODO: Better error messages
        //Tries to store the medic's CV in the appropriate directory
        //Returns error message if failed, null otherwise
        public static function storeMedicCV(string $file_key, int $medic_user_id): string|null{
            //File validation
            $accepted_formats = ["application/pdf"];
            $file_max_size = 1024 * 300; // 300KB
            $err = self::validateFile($file_key, $accepted_formats, $file_max_size);
            if ($err)
                return $err;
            
            //Move file to the appropriate directory
            $file_name = "CV." . self::getFileExtension($_FILES[$file_key]["name"]);
            $dest_dir = "documents/medics/$medic_user_id/";
            $err = self::storeFile($file_key, $dest_dir, $file_name);
            return $err;
        }

        public static function displayMedicCV(int $medic_user_id): string|null{
            $file_path = "documents/medics/$medic_user_id/CV.pdf";
            return self::displayFile($file_path);
        }

        public static function storeHiringContract(string $file_key, int $hirer_user_id, int $applicant_user_id): string|null{
            //File validation
            $accepted_formats = ["application/pdf"];
            $file_max_size = 1024 * 500; // 500KB
            $err = self::validateFile($file_key, $accepted_formats, $file_max_size);
            if ($err)
                return $err;
            
            //Move file to the appropriate directory
            $file_name = "contract" . self::getFileExtension($_FILES[$file_key]["name"]);
            $dest_dir = "documents/hospitals/$hirer_user_id/$applicant_user_id/";
            $err = self::storeFile($file_key, $dest_dir, $file_name);
            return $err;
        }

        public static function createAppointmentSummaryPDF(int $appointment_id, array $data): string|null{
            $title = "Appointment Summary";
            $storage_path = self::getAppointmentSummaryPath($appointment_id);

            //Create new directory if needed
            if (!is_dir($storage_path))
                if (!mkdir($storage_path, 0777, true))
                    return "Failed to create directory";

            $storage_path = $storage_path . "summary.pdf";
            //Create and store the pdf
            self::createPDF($storage_path, $title, $data);
            return null;
        }

        public static function displayAppointmentSummaryPDF(int $appointment_id): string|null{
            $file_path = self::getAppointmentSummaryPath($appointment_id) . "summary.pdf";
            return self::displayFile($file_path);
        }

        private static function displayFile(string $file_path): string|null{
            if (!file_exists($file_path))
                return "File not found";
            header("Content-Type: application/pdf");
            header("Content-Disposition: inline; filename=summary.pdf");
            readfile($file_path);
            return null;
        }


        //Creates a pdf file with the given data and stores it in the given path
        private static function createPDF(string $storage_path, string $title, array $data){
            require_once 'deps/FPDF/fpdf.php';

            //Creating pdf
            $pdf = new FPDF();
            $pdf->AddPage();

            //Setting the header
            self::createPDFHeader($pdf, $title);
            $padding = 10;

            foreach ($data as $k => $v){
                $page_width = $pdf->GetPageWidth();
                $full_String = $k . ':    ' . $v;
                $pdf->MultiCell($page_width - $padding, 10, $full_String, 0, 'L');
            }

            //Store the pdf in $storage_path
            $pdf->Output($storage_path, "F");
        }

        private static function createPDFHeader($pdf, $title){
            //Setting the header
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Image('public/img/hosplive_logo.jpg', 0, 0, 25, 25);
            $pdf->Cell(60);
            $pdf->Cell(40, 10, $title);
            $pdf->Ln(50);
        }

        public static function getAppointmentSummaryPath(int $appointment_id): string{
            return "documents/appointments/$appointment_id/";
        }
        
        private static function getFileExtension(string $file_name): string{
            return substr($file_name, strrpos($file_name, ".") + 1);
        }

        private static function storeFile(string $file_key, string $dest_dir, string $file_name): string|null{
            //Create new directory if needed
            if (!is_dir($dest_dir))
                if (!mkdir($dest_dir, 0777, true)){
                    return "Failed to create directory";
                }
            $src_dir = $_FILES[$file_key]["tmp_name"];
            if (!move_uploaded_file($src_dir, $dest_dir . $file_name))
                return "Failed to move file";

            return null;
        }

        private static function validateFile(string $file_key, array $accepted_formats, int $file_max_size): string|null{
            //Check if file exists
            if (!isset($_FILES[$file_key]))
                return "File not found";

            $file_obj = $_FILES[$file_key];
            if ($file_obj["error"] != 0)
                return "File upload error";

            //TODO: Check for mime type
            //Check for appropriate file format
            if (!in_array($file_obj["type"], $accepted_formats))
                return "File is not in an accepted format(" . implode(', ', $accepted_formats) . ")";

            //TO DO convert from bytes to a more readable format
            //Check for file size
            if ($file_obj["size"] > $file_max_size)
                return "File is too big";

            return null;
        }

    }
?>