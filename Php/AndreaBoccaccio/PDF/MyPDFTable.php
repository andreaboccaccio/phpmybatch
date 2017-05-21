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
class Php_AndreaBoccaccio_PDF_MyPDFTable extends Php_AndreaBoccaccio_PDF_SimplePDFTable {
	
	private $pageHeadersSize = 0;
	
	private $pageHeaders = array();
	
	private $pageFootersSize = 0;
	
	private $pageFooters = array();
	
	private $pageFooterStart = 15;
	
	protected function getPageHeader($k,$subK) {
		$ret = null;
		$tmpArray = array();
		
		if(array_key_exists($k, $this->pageHeaders)) {
			$tmpArray = $this->pageHeaders[$k];
			if(array_key_exists($subK, $tmpArray)) {
				$ret = $tmpArray[$subK];
			}
		}
		
		return $ret;
	}
	
	protected function getPageFooter($k,$subK) {
		$ret = null;
		$tmpArray = array();
	
		if(array_key_exists($k, $this->pageFooters)) {
			$tmpArray = $this->pageFooters[$k];
			if(array_key_exists($subK, $tmpArray)) {
				$ret = $tmpArray[$subK];
			}
		}
	
		return $ret;
	}
	
	protected function renderPageHeader($k) {
		$tmpArray = array();
		$kind = $this->getPageHeader($k, 'kind');
		
		if((strncmp($kind, 'Cell', strlen('Cell')) == 0)
			||(strncmp($kind, 'MultiCell', strlen('MultiCell')) == 0)) {
			$this->setFont($this->getPageHeader($k,'fontFamily')
					,$this->getPageHeader($k,'fontStyle')
					,$this->getPageHeader($k,'fontSize')
					);
			$tmpArray = preg_split('/,+/', $this->getPageHeader($k,'textColor'));
			$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
			if($this->getPageHeader($k,'fill')) {
				$tmpArray = preg_split('/,+/', $this->getPageHeader($k,'fillColor'));
				$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
			}
			if($this->getPageHeader($k,'y')!=null) {
				$this->SetY($this->getPageHeader($k,'y'));
			}
			if($this->getPageHeader($k,'x')!=null) {
				$this->SetX($this->getPageHeader($k,'x'));
			}
			if(strncmp($kind, 'Cell', strlen('Cell')) == 0) {
				$this->Cell($this->getPageHeader($k,'wSize')
						,$this->getPageHeader($k,'hSize')
						,$this->getPageHeader($k,'display')
						,$this->getPageHeader($k,'border')
						,$this->getPageHeader($k,'ln')
						,$this->getPageHeader($k,'align')
						,$this->getPageHeader($k,'fill')
						,$this->getPageHeader($k,'link')
						);
			} else {
				$this->MultiCell($this->getPageHeader($k,'wSize')
						,$this->getPageHeader($k,'hSize')
						,$this->getPageHeader($k,'display')
						,$this->getPageHeader($k,'border')
						,$this->getPageHeader($k,'align')
						,$this->getPageHeader($k,'fill')
				);
				if(intval($this->getPageHeader($k,'ln')) == 1) {
					$this->Ln();
				}
			}
		} else if(strncmp($kind, 'Image', strlen('Image')) == 0) {
			$this->Image($this->getPageHeader($k,'display')
					,$this->getPageHeader($k,'x')
					,$this->getPageHeader($k,'y')
					,$this->getPageHeader($k,'wSize')
					,$this->getPageHeader($k,'hSize')
					,$this->getPageHeader($k,'type')
					,$this->getPageHeader($k,'link')
			);
		}
			
	}
	
	protected function renderPageFooter($k) {
		$tmpArray = array();
		$kind = $this->getPageFooter($k, 'kind');
	
		if((strncmp($kind, 'Cell', strlen('Cell')) == 0)
				||(strncmp($kind, 'MultiCell', strlen('MultiCell')) == 0)) {
			$this->setFont($this->getPageFooter($k,'fontFamily')
					,$this->getPageFooter($k,'fontStyle')
					,$this->getPageFooter($k,'fontSize')
			);
			$tmpArray = preg_split('/,+/', $this->getPageFooter($k,'textColor'));
			$this->SetTextColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
			if($this->getPageFooter($k,'fill')) {
				$tmpArray = preg_split('/,+/', $this->getPageFooter($k,'fillColor'));
				$this->SetFillColor(intval($tmpArray[0]),intval($tmpArray[1]),intval($tmpArray[2]));
			}
			if($this->getPageFooter($k,'y')!=null) {
				$this->SetY($this->getPageFooter($k,'y'));
			}
			if($this->getPageFooter($k,'x')!=null) {
				$this->SetX($this->getPageFooter($k,'x'));
			}
			if(strncmp($kind, 'Cell', strlen('Cell')) == 0) {
				$this->Cell($this->getPageFooter($k,'wSize')
						,$this->getPageFooter($k,'hSize')
						,$this->getPageFooter($k,'display')
						,$this->getPageFooter($k,'border')
						,$this->getPageFooter($k,'ln')
						,$this->getPageFooter($k,'align')
						,$this->getPageFooter($k,'fill')
						,$this->getPageFooter($k,'link')
				);
			} else {
				$this->MultiCell($this->getPageFooter($k,'wSize')
						,$this->getPageFooter($k,'hSize')
						,$this->getPageFooter($k,'display')
						,$this->getPageFooter($k,'border')
						,$this->getPageFooter($k,'align')
						,$this->getPageFooter($k,'fill')
				);
				if(intval($this->getPageFooter($k,'ln')) == 1) {
					$this->Ln();
				}
			}
		} else if(strncmp($kind, 'Image', strlen('Image')) == 0) {
			$this->Image($this->getPageFooter($k,'display')
					,$this->getPageFooter($k,'x')
					,$this->getPageFooter($k,'y')
					,$this->getPageFooter($k,'wSize')
					,$this->getPageFooter($k,'hSize')
					,$this->getPageFooter($k,'type')
					,$this->getPageFooter($k,'link')
			);
		}
			
	}
	
	protected function getPageHeadersSize() {
		return $this->pageHeadersSize;
	}
	
	protected function getPageFootersSize() {
		return $this->pageFootersSize;
	}
	
	protected function getPageFooterStart() {
		return $this->pageFooterStart;
	}
	
	public function addPageHeader($kind
			,$x
			,$y
			,$wSize
			,$hSize
			,$display
			,$fontFamily
			,$fontStyle
			,$fontSize
			,$align
			,$textColor
			,$fill
			,$fillColor
			,$border
			,$ln
			,$type=null
			,$link=null) {
		$this->pageHeaders[$this->pageHeadersSize] = array();
		$this->pageHeaders[$this->pageHeadersSize]["kind"] = $kind;
		$this->pageHeaders[$this->pageHeadersSize]["x"] = $x;
		$this->pageHeaders[$this->pageHeadersSize]["y"] = $y;
		$this->pageHeaders[$this->pageHeadersSize]["wSize"] = $wSize;
		$this->pageHeaders[$this->pageHeadersSize]["hSize"] = $hSize;
		$this->pageHeaders[$this->pageHeadersSize]["display"] = $display;
		$this->pageHeaders[$this->pageHeadersSize]["fontFamily"] = $fontFamily;
		$this->pageHeaders[$this->pageHeadersSize]["fontStyle"] = $fontStyle;
		$this->pageHeaders[$this->pageHeadersSize]["fontSize"] = $fontSize;
		$this->pageHeaders[$this->pageHeadersSize]["align"] = $align;
		$this->pageHeaders[$this->pageHeadersSize]["textColor"] = $textColor;
		$this->pageHeaders[$this->pageHeadersSize]["fill"] = $fill;
		$this->pageHeaders[$this->pageHeadersSize]["fillColor"] = $fillColor;
		$this->pageHeaders[$this->pageHeadersSize]["border"] = $border;
		$this->pageHeaders[$this->pageHeadersSize]["ln"] = $ln;
		if($type != null) {
			$this->pageHeaders[$this->pageHeadersSize]["type"] = $type;
		}
		if($link != null) {
			$this->pageHeaders[$this->pageHeadersSize]["link"] = $link;
		}
		
		$this->pageHeadersSize += 1;
	}
	
	public function addPageFooter($kind
			,$x
			,$y
			,$wSize
			,$hSize
			,$display
			,$fontFamily
			,$fontStyle
			,$fontSize
			,$align
			,$textColor
			,$fill
			,$fillColor
			,$border
			,$ln
			,$type=null
			,$link=null) {
		$this->pageFooters[$this->pageFootersSize] = array();
		$this->pageFooters[$this->pageFootersSize]["kind"] = $kind;
		$this->pageFooters[$this->pageFootersSize]["x"] = $x;
		$this->pageFooters[$this->pageFootersSize]["y"] = $y;
		$this->pageFooters[$this->pageFootersSize]["wSize"] = $wSize;
		$this->pageFooters[$this->pageFootersSize]["hSize"] = $hSize;
		$this->pageFooters[$this->pageFootersSize]["display"] = $display;
		$this->pageFooters[$this->pageFootersSize]["fontFamily"] = $fontFamily;
		$this->pageFooters[$this->pageFootersSize]["fontStyle"] = $fontStyle;
		$this->pageFooters[$this->pageFootersSize]["fontSize"] = $fontSize;
		$this->pageFooters[$this->pageFootersSize]["align"] = $align;
		$this->pageFooters[$this->pageFootersSize]["textColor"] = $textColor;
		$this->pageFooters[$this->pageFootersSize]["fill"] = $fill;
		$this->pageFooters[$this->pageFootersSize]["fillColor"] = $fillColor;
		$this->pageFooters[$this->pageFootersSize]["border"] = $border;
		$this->pageFooters[$this->pageFootersSize]["ln"] = $ln;
		if($type != null) {
			$this->pageFooters[$this->pageFootersSize]["type"] = $type;
		}
		if($link != null) {
			$this->pageFooters[$this->pageFootersSize]["link"] = $link;
		}
		
		$this->pageFootersSize += 1;
	}
	
	public function setPageFooterStart($pfs) {
		$this->pageFooterStart = $pfs;
	}
	
	function Header() {
		$i = -1;
		$max = -1;
		$sqlId = '';
		$tmpArray = array();
		$oldX = 0;
		$oldY = 0;
		$newX = 0;
		$newY = 0;
		
		$max=$this->getPageHeadersSize();
		for($i = 0; $i < $max; ++$i) {
			$this->renderPageHeader($i);
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
				//$oldY = $this->GetY();
				//$oldX = $this->GetX();
				//$this->MultiCell($this->getColumnInfo($sqlId, 'wSize')
				//		,$this->getRowInfo('headerHSize')
				//		,$this->getColumnInfo($sqlId,'header','display')
				//		,$this->getColumnInfo($sqlId,'header','border')
				//		,$this->getColumnInfo($sqlId,'header','align')
				//		,$this->getColumnInfo($sqlId,'header','fill')
				//		);
				$this->Cell($this->getColumnInfo($sqlId, 'wSize')
						,$this->getRowInfo('headerHSize')
						,$this->getColumnInfo($sqlId,'header','display')
						,$this->getColumnInfo($sqlId,'header','border')
						,0
						,$this->getColumnInfo($sqlId,'header','align')
						,$this->getColumnInfo($sqlId,'header','fill')
				);
				//$newY = $oldY;
				//$newX = $oldX + $this->getColumnInfo($sqlId, 'wSize');
				//$this->setY($newY);
				//$this->setX($newX);
			}
			$this->Ln();
		}
	}
	
	function Footer() {
		$i = -1;
		$max = -1;
		
		$this->SetY(-$this->getPageFooterStart());
		if($this->getPageFootersSize()>0) {
			$max=$this->getPageFootersSize();
			for($i = 0; $i < $max; ++$i) {
				$this->renderPageFooter($i);
			}
		} else {
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Pagina ' . $this->PageNo() . ' di {nb}','T',0,'C');
		}
	}
}