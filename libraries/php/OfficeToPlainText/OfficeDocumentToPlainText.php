<?php
class OfficeDocumentToPlainText{
    /*** Class variables ***/
    private $filename;

    /*** Constructor ***/
    public function __construct($filePath)
    {
        $this->filename = $filePath;
    }

    /****************************************** Read .doc document ******************************************/
    private function readDoc()
    {
        if(($fh = fopen($this->filename, 'r')) !== false ) {
            $headers = fread($fh, 0xA00);
            $n1 = ( ord($headers[0x21C]) - 1 );// 1 = (ord(n)*1) ; Document has from 0 to 255 characters
            $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );// 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
            $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );// 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
            $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );// 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
            $textLength = ($n1 + $n2 + $n3 + $n4);// Total length of text in the document

            if($textLength > 0)
            {
                $extracted_plaintext = fread($fh, $textLength);
                $extracted_plaintext = mb_convert_encoding($extracted_plaintext,'UTF-8');

                return ($extracted_plaintext);
            }
        }

        return false;
    }

    /****************************************** Read .docx document *****************************************/
    private function readDOCX(){
        $content = '';

        $zip = zip_open($this->filename);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip))
        {

            if (zip_entry_open($zip, $zip_entry) == FALSE)
                continue;

            if (zip_entry_name($zip_entry) != "word/document.xml")
                continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    /****************************************** Read .xlsx document *****************************************/

    private function readXLSX()
    {
        $xml_filename = "xl/sharedStrings.xml"; //content file name
        $zip_handle = new ZipArchive;
        $output_text = "";
        if(true === $zip_handle->open($this->filename))
        {
            if(($xml_index = $zip_handle->locateName($xml_filename)) !== false)
            {
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                $DOM = new DOMDocument();
                $ok = $DOM->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                if($ok)
                {
                    $xml = $DOM->saveXML();
                    $output_text = strip_tags($xml);
                }else
                {
                    return false;
                }
            }

            $zip_handle->close();
        }

        if(!empty($output_text))
        {
            return $output_text;
        }else return false;
    }

    /****************************************** Read .pptx document *****************************************/
    private function readPPTX()
    {
        $zip_handle = new ZipArchive;
        $output_text = "";
        if(true === $zip_handle->open($this->filename)){
            $slide_number = 1; //loop through slide files
            $DOM = new DOMDocument();

            while(($xml_index = $zip_handle->locateName("ppt/slides/slide".$slide_number.".xml")) !== false)
            {
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                $ok = $DOM->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                if($ok)
                {
                    $output_text .= strip_tags($DOM->saveXML());
                }

                $slide_number++;
            }

            $zip_handle->close();
        }
        if(!empty($output_text))
        {
            return $output_text;
        }else return false;
    }


    public function getDocumentText() {

        if(isset($this->filename) && !file_exists($this->filename)) {
            return false;
        }

        $fileArray = pathinfo($this->filename);
        $file_ext  = $fileArray['extension'];
        
        if(in_array($file_ext, ['doc', 'docx', 'xlsx', 'pptx']))
        {
            if($file_ext == "doc")
            {
                return $this->readDoc();
            }
            else if($file_ext == "docx")
            {
                return $this->readDOCX();
            }
            else if($file_ext == "xlsx")
            {
                return $this->readXLSX();
            }
            else if($file_ext == "pptx")
            {
                return $this->readPPTX();
            }
        }

        return false;
    }
}
?>