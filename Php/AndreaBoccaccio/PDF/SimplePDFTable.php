<?php
/*
 * phpmywhs - An open source warehouse management software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.com
 * 
 * This file is part of phpmywhs.
 * 
 * phpmywhs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpmywhs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with phpmywhs. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_PDF_SimplePDFTable extends FPDF {
	
	private $tableHeaders = array();
	
	private $repeatTableHeaders = FALSE;
	
	private $tableTitle = '';
	
	private $rowInfos = array();
	
	private $columnInfos = array();
	
	public function addHeader($header) {
		$i = count($this->tableHeaders);
		
		$this->tableHeaders[$i] = $header;
	}
	
	public function setTableTitle($tableTitle) {
		$this->tableTitle = strval($tableTitle);
	}
	
	public function setRepeatTableHeader($repeat) {
		$this->repeatTableHeaders = $repeat;
	}
	
	public function setRowInfo($headerHSize, $bodyHSize) {
		if(!array_key_exists("headerHSize", $this->rowInfos)) {
			$this->rowInfos["headerHSize"] = floatval($headerHSize);
		}
		if(!array_key_exists("bodyHSize", $this->rowInfos)) {
			$this->rowInfos["bodyHSize"] = floatval($bodyHSize);
		}
	}
	
	public function addColumnInfo($sqlId
			,$wSize
			,$hdrDisplay
			,$hdrFontFamily
			,$hdrFontStyle
			,$hdrFontSize
			,$hdrAlign
			,$hdrTextColor
			,$hdrFill
			,$hdrFillColor
			,$hdrBorder
			,$bdyFontFamily
			,$bdyFontStyle
			,$bdyFontSize
			,$bdyAlign
			,$bdyTextColor
			,$bdyFill
			,$bdyFillAColor
			,$bdyFillBColor
			,$bdyBorder
			,$bdyFormat
			) {
		$tmpArray = array();
		$tmpHdr = array();
		$tmpBdy = array();
		
		if(!array_key_exists($sqlId, $this->columnInfos)) {
			$tmpHdr["display"] = $hdrDisplay;
			$tmpHdr["fontFamily"] = $hdrFontFamily;
			$tmpHdr["fontStyle"] = $hdrFontStyle;
			$tmpHdr["fontSize"] = $hdrFontSize;
			$tmpHdr["align"] = $hdrAlign;
			$tmpHdr["textColor"] = $hdrTextColor;
			$tmpHdr["fill"] = $hdrFill;
			$tmpHdr["fillColor"] = $hdrFillColor;
			$tmpHdr["border"] = $hdrBorder;
			$tmpBdy["fontFamily"] = $bdyFontFamily;
			$tmpBdy["fontStyle"] = $bdyFontStyle;
			$tmpBdy["fontSize"] = $bdyFontSize;
			$tmpBdy["align"] = $bdyAlign;
			$tmpBdy["textColor"] = $bdyTextColor;
			$tmpBdy["fill"] = $bdyFill;
			$tmpBdy["fillAColor"] = $bdyFillAColor;
			$tmpBdy["fillBColor"] = $bdyFillBColor;
			$tmpBdy["border"] = $bdyBorder;
			$tmpBdy["format"] = $bdyFormat;
			$tmpArray["header"] = $tmpHdr;
			$tmpArray["body"] = $tmpBdy;
			$tmpArray["wSize"] = $wSize;
			$this->columnInfos[$sqlId] = $tmpArray;
		}
	}
	
	function Header() {
		$i = -1;
		$max = -1;
		$sqlId = '';
		$tmpArray = array();
		
		if(strlen($this->tableTitle)> 0) {
			$this->SetFont('Arial','B',16);
			$this->Cell(40,0,$this->tableTitle,0,'C');
			$this->Ln(15);
		}
		//var_dump($this->columnInfos);
		if($this->repeatTableHeaders) {
			$max = count($this->tableHeaders);
			for($i = 0; $i < $max; ++$i) {
				$sqlId = $this->tableHeaders[$i];
				$this->setFont($this->columnInfos[$sqlId]["header"]["fontFamily"]
						,$this->columnInfos[$sqlId]["header"]["fontStyle"]
						,$this->columnInfos[$sqlId]["header"]["fontSize"]
						);
				$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["header"]["textColor"]);
				$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				if($this->columnInfos[$sqlId]["header"]["fill"]) {
					$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["header"]["fillColor"]);
					$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				}
				$this->Cell($this->columnInfos[$sqlId]["wSize"]
						,$this->rowInfos["headerHSize"]
						,$this->columnInfos[$sqlId]["header"]["display"]
						,$this->columnInfos[$sqlId]["header"]["border"]
						,0
						,$this->columnInfos[$sqlId]["header"]["align"]
						,$this->columnInfos[$sqlId]["header"]["fill"]
						);
			}
			$this->Ln();
		}
	}
	
	function Footer() {
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Pagina ' . $this->PageNo() . ' di {nb}','T',0,'C');
	}
	
	public function simpleTable(&$data) {
		$nRow = 0;
		
		//var_dump($this->columnInfos);
		if(!$this->repeatTableHeaders) {
			$max = count($this->tableHeaders);
			for($i = 0; $i < $max; ++$i) {
				$sqlId = $this->tableHeaders[$i];
				$this->setFont($this->columnInfos[$sqlId]["header"]["fontFamily"]
						,$this->columnInfos[$sqlId]["header"]["fontStyle"]
						,$this->columnInfos[$sqlId]["header"]["fontSize"]
				);
				$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["header"]["textColor"]);
				$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				if($this->columnInfos[$sqlId]["header"]["fill"]) {
					$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["header"]["fillColor"]);
					$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				}
				$this->Cell($this->columnInfos[$sqlId]["wSize"]
						,$this->rowInfos["headerHSize"]
						,$this->columnInfos[$sqlId]["header"]["display"]
						,$this->columnInfos[$sqlId]["header"]["border"]
						,0
						,$this->columnInfos[$sqlId]["header"]["align"]
						,$this->columnInfos[$sqlId]["header"]["fill"]
				);
			}
			$this->Ln();
		}
		foreach($data as $row) {
			$max = count($this->tableHeaders);
			for($i = 0; $i < $max; ++$i) {
				$sqlId = $this->tableHeaders[$i];
				$this->setFont($this->columnInfos[$sqlId]["body"]["fontFamily"]
						,$this->columnInfos[$sqlId]["body"]["fontStyle"]
						,$this->columnInfos[$sqlId]["body"]["fontSize"]
				);
				$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["body"]["textColor"]);
				$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				if($this->columnInfos[$sqlId]["body"]["fill"]) {
					if(($nRow%2)==0) {
						$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["body"]["fillAColor"]);
					} else {
						$tmpArray = preg_split('/,+/', $this->columnInfos[$sqlId]["body"]["fillBColor"]);
					}
					$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				}
				$this->Cell($this->columnInfos[$sqlId]["wSize"]
						,$this->rowInfos["bodyHSize"]
						,$row[$sqlId]
						,$this->columnInfos[$sqlId]["body"]["border"]
						,0
						,$this->columnInfos[$sqlId]["body"]["align"]
						,$this->columnInfos[$sqlId]["body"]["fill"]
				);
			}
			$this->Ln();
			++$nRow;
		}
	}
}