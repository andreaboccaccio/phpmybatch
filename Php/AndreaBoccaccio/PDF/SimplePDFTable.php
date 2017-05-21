<?php
/*
 * phpmybatch - An open source batches of goods management system software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.it
 * 
 * This file is part of phpmybatch.
 * 
 * phpmybatch is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpmybatch is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with phpmywhs. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_PDF_SimplePDFTable extends FPDF {
	
	private $tableHeadersSize = 0;
	
	private $tableHeaders = array();
	
	private $repeatTableHeaders = FALSE;
	
	private $tableTitle = '';
	
	private $rowInfos = array();
	
	private $columnInfos = array();
	
	private $rowsPerPage = -1;
	
	protected function getTableHeadersSize() {
		return $this->tableHeadersSize;
	}
	
	protected function getTableHeader($i) {
		$ret=null;
		
		if(array_key_exists($i, $this->tableHeaders)) {
			$ret=$this->tableHeaders[$i];
		}
		return $ret;
	}
	
	protected function getRepeatTableHeaders() {
		return $this->repeatTableHeaders;
	}
	
	protected function getTableTitle() {
		return $this->tableTitle;
	}
	
	protected function getRowInfo($k) {
		$ret=null;
		
		if(array_key_exists($k, $this->rowInfos)) {
			$ret=$this->rowInfos[$k];
		}
		return $ret;
	}
	
	protected function getColumnInfo($sqlId,$k,$subK=null) {
		$ret=null;
		$tmpArray = array();
		$tmpSubArray = array();
	
		if(array_key_exists($sqlId, $this->columnInfos)) {
			$tmpArray = $this->columnInfos[$sqlId];
			if(strncmp(strtolower($k), 'wsize', strlen('wsize')) == 0) {
				$ret = $tmpArray["wSize"];
			} elseif((array_key_exists($k, $tmpArray))&&($subK != null)) {
				$tmpSubArray = $tmpArray[$k];
				if(array_key_exists($subK, $tmpSubArray)) {
					$ret = $tmpSubArray[$subK];
				}
			}
		}
		return $ret;
	}
	
	protected function getRowsPerPage() {
		return $this->rowsPerPage;
	}
	
	public function addHeader($header) {
		$i = $this->getTableHeadersSize();
		
		$this->tableHeaders[$i] = $header;
		$this->tableHeadersSize += 1;
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
	
	public function setRowsPerPage($nRows) {
		if($nRows > 0) {
			$this->rowsPerPage = intval($nRows);
			$this->SetAutoPageBreak(FALSE);
		}
	}
	
	function Header() {
		$i = -1;
		$max = -1;
		$sqlId = '';
		$tmpArray = array();
		
		if(strlen($this->getTableTitle())> 0) {
			$this->SetFont('Arial','B',16);
			$this->Cell(40,0,$this->getTableTitle(),0,'C');
			$this->Ln(15);
		}
		//var_dump($this->columnInfos);
		if($this->getRepeatTableHeaders()) {
			$max = $this->getTableHeadersSize();
			for($i = 0; $i < $max; ++$i) {
				$sqlId = $this->getTableHeader($i);
				$this->setFont($this->getColumnInfo($sqlId,'header','fontFamily')
						,$this->getColumnInfo($sqlId,'header','fontStyle')
						,$this->getColumnInfo($sqlId,'header','fontSize')
						);
				$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'header','textColor'));
				$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				if($this->getColumnInfo($sqlId, 'header','fill')) {
					$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'header','fillColor'));
					$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				}
				$this->Cell($this->getColumnInfo($sqlId, 'wSize')
						,$this->getRowInfo('headerHSize')
						,$this->getColumnInfo($sqlId,'header','display')
						,$this->getColumnInfo($sqlId,'header','border')
						,0
						,$this->getColumnInfo($sqlId,'header','align')
						,$this->getColumnInfo($sqlId,'header','fill')
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
		if(!$this->getRepeatTableHeaders()) {
			$max = $this->getTableHeadersSize();
			for($i = 0; $i < $max; ++$i) {
				$sqlId = $this->getTableHeader($i);
				$this->setFont($this->getColumnInfo($sqlId,'header','fontFamily')
						,$this->getColumnInfo($sqlId,'header','fontStyle')
						,$this->getColumnInfo($sqlId,'header','fontSize')
						);
				$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'header','textColor'));
				$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				if($this->getColumnInfo($sqlId, 'header','fill')) {
					$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'header','fillColor'));
					$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				}
				$this->Cell($this->getColumnInfo($sqlId, 'wSize')
						,$this->getRowInfo('headerHSize')
						,$this->getColumnInfo($sqlId,'header','display')
						,$this->getColumnInfo($sqlId,'header','border')
						,0
						,$this->getColumnInfo($sqlId,'header','align')
						,$this->getColumnInfo($sqlId,'header','fill')
						);
			}
			$this->Ln();
		}
		foreach($data as $row) {
			$max = $this->getTableHeadersSize();
			for($i = 0; $i < $max; ++$i) {
				$sqlId = $this->getTableHeader($i);
				$this->setFont($this->getColumnInfo($sqlId,'body','fontFamily')
						,$this->getColumnInfo($sqlId,'body','fontStyle')
						,$this->getColumnInfo($sqlId,'body','fontSize')
				);
				$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'body','textColor'));
				$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				if($this->getColumnInfo($sqlId,'body','fill')) {
					if(($nRow%2)==0) {
						$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'body','fillAColor'));
					} else {
						$tmpArray = preg_split('/,+/', $this->getColumnInfo($sqlId,'body','fillBColor'));
					}
					$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
				}
				$this->Cell($this->getColumnInfo($sqlId, 'wSize')
						,$this->getRowInfo('bodyHSize')
						,$row[$sqlId]
						,$this->getColumnInfo($sqlId,'body','border')
						,0
						,$this->getColumnInfo($sqlId,'body','align')
						,$this->getColumnInfo($sqlId,'body','fill')
				);
			}
			$this->Ln();
			++$nRow;
			if($this->getRowsPerPage()>0) {
				if($nRow > 0) {
					if($nRow%($this->getRowsPerPage()) == 0) {
						$this->AddPage();
					}
				}
			}
		}
	}
}