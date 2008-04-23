<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

	define("DEFAULTXMLSTARTELEMENTHANDLER", "");
	define("DEFAULTXMLENDELEMENTHANDLER", "");
	define("DEFAULTXMLERRORHANDLER", "");
	define("FILEOPENERROR","2");
	
	// parses an XML file using the handlers defined in the functions, whose names are contained in
	// $startelementhandler, $endelementhandler, $datahandler
	function parsexml($xmlfile, $xmlstartelementhandler, $xmlendelementhandler, $xmldatahandler, $xmlerrorhandler) {
		global $xmlcurrentelement,$xmlelementattrs;
		
		set_magic_quotes_runtime(0); // important, otherwise element attributes lead to an error
		$xml_parser = xml_parser_create();
	
		// set default element handlers in case the user hasn't specified one
		if ($xmlstartelementhandler==DEFAULTXMLSTARTELEMENTHANDLER) { $xmlstartelementhandler = "xmlstartelement"; }
		if ($xmlendelementhandler==DEFAULTXMLENDELEMENTHANDLER) { $xmlendelementhandler = "xmlendelement"; }
		if ($xmlerrorhandler==DEFAULTXMLERRORHANDLER) { $xmlerrorhandler = "xmlerror"; }
	
		xml_set_element_handler($xml_parser, $xmlstartelementhandler, $xmlendelementhandler);
		xml_set_character_data_handler($xml_parser, $xmldatahandler);
	
		if ($fp = @fopen($xmlfile, "r")) {
			$xmlcurrentelement = "";
			$xmlelementattrs = "";
			$error = 0;
			while (!$error && $data = fread($fp, 65536)) { // this is a potential bug since it may split the file at 64K. need to find better solution
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					$xmlerrorhandler($xml_parser);
					$error = 1;
				}
			}
			xml_parser_free($xml_parser);
		} 
		else { $error = FILEOPENERROR; }
		
		return $error;
	} // end: function parsexml
	
	// default error handler
	function xmlerror($xml_parser) {
		sprintf("XML error: %s at line %d",
						xml_error_string(xml_get_error_code($xml_parser)),
						xml_get_current_line_number($xml_parser));
	} // end: function xmlerror
	
	// XML parser element handler for start element
	function xmlstartelement($parser, $element, $attrs) {
		global $xmlcurrentelement,$xmlelementattrs;
	
		$xmlcurrentelement = $element;
		$xmlelementattrs = $attrs;
	}
	
	// XML parser element handler for end element
	function xmlendelement($parser, $element) {
		global $xmlcurrentelement,$xmlelementattrs;
	
		$xmlcurrentelement = "";
		$xmlelementattrs = "";
	}
?>