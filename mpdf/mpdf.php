<?php

// ******************************************************************************
// Software: mPDF, Unicode-HTML Free PDF generator                              *
// Version:  6.0        based on                                                *
//           FPDF by Olivier PLATHEY                                            *
//           HTML2FPDF by Renato Coelho                                         *
// Date:     2014-11-24                                                         *
// Author:   Ian Back <ianb@bpm1.com>                                           *
// License:  GPL                                                                *
//                                                                              *
// Changes:  See changelog.txt                                                  *
// ******************************************************************************

define('mPDF_VERSION', '6.0');

//Scale factor
define('_MPDFK', (72 / 25.4));

// Specify which font metrics to use:
// 'winTypo' uses sTypoAscender etc from the OS/2 table and is the one usually recommended - BUT
// 'win' use WinAscent etc from OS/2 and inpractice seems to be used more commonly in Windows environment
// 'mac' uses Ascender etc from hhea table, and is used on Mac/OSX environment
if (!defined('_FONT_DESCRIPTOR'))
	define("_FONT_DESCRIPTOR", 'win'); // Values: '' [BLANK] or 'win', 'mac', 'winTypo'

	/* -- HTML-CSS -- */

define('_BORDER_ALL', 15);
define('_BORDER_TOP', 8);
define('_BORDER_RIGHT', 4);
define('_BORDER_BOTTOM', 2);
define('_BORDER_LEFT', 1);
/* -- END HTML-CSS -- */

// mPDF 6.0
// Used for $textvars - user settings via CSS
define('FD_UNDERLINE', 1); // font-decoration
define('FD_LINETHROUGH', 2);
define('FD_OVERLINE', 4);
define('FA_SUPERSCRIPT', 8); // font-(vertical)-align
define('FA_SUBSCRIPT', 16);
define('FT_UPPERCASE', 32); // font-transform
define('FT_LOWERCASE', 64);
define('FT_CAPITALIZE', 128);
define('FC_KERNING', 256); // font-(other)-controls
define('FC_SMALLCAPS', 512);


if (!defined('_MPDF_PATH'))
	define('_MPDF_PATH', dirname(preg_replace('/\\\\/', '/', __FILE__)) . '/');
if (!defined('_MPDF_URI'))
	define('_MPDF_URI', _MPDF_PATH);

require_once(_MPDF_PATH . 'includes/functions.php');
require_once(_MPDF_PATH . 'config_lang2fonts.php');

require_once(_MPDF_PATH . 'classes/ucdn.php'); // mPDF 6.0

/* -- OTL -- */
require_once(_MPDF_PATH . 'classes/indic.php'); // mPDF 6.0
require_once(_MPDF_PATH . 'classes/myanmar.php'); // mPDF 6.0
require_once(_MPDF_PATH . 'classes/sea.php'); // mPDF 6.0
/* -- END OTL -- */


if (!defined('_JPGRAPH_PATH'))
	define("_JPGRAPH_PATH", _MPDF_PATH . 'jpgraph/');

if (!defined('_MPDF_TEMP_PATH'))
	define("_MPDF_TEMP_PATH", _MPDF_PATH . 'tmp/');

if (!defined('_MPDF_TTFONTPATH')) {
	define('_MPDF_TTFONTPATH', _MPDF_PATH . 'ttfonts/');
}
if (!defined('_MPDF_TTFONTDATAPATH')) {
	define('_MPDF_TTFONTDATAPATH', _MPDF_PATH . 'ttfontdata/');
}

$errorlevel = error_reporting();
$errorlevel = error_reporting($errorlevel & ~E_NOTICE);

//error_reporting(E_ALL);

if (function_exists("date_default_timezone_set")) {
	if (ini_get("date.timezone") == "") {
		date_default_timezone_set("Europe/London");
	}
}
if (!function_exists("mb_strlen")) {
	die("Error - mPDF requires mb_string functions. Ensure that PHP is compiled with php_mbstring.dll enabled.");
}

if (!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

class mPDF
{

	///////////////////////////////
	// EXTERNAL (PUBLIC) VARIABLES
	// Define these in config.php
	///////////////////////////////
	var $useFixedNormalLineHeight; // mPDF 6
	var $useFixedTextBaseline; // mPDF 6
	var $adjustFontDescLineheight; // mPDF 6
	var $interpolateImages; // mPDF 6
	var $defaultPagebreakType; // mPDF 6 pagebreaktype
	var $indexUseSubentries; // mPDF 6

	var $autoScriptToLang; // mPDF 6
	var $baseScript; // mPDF 6
	var $autoVietnamese; // mPDF 6
	var $autoArabic; // mPDF 6

	var $CJKforceend;
	var $h2bookmarks;
	var $h2toc;
	var $decimal_align;
	var $margBuffer;
	var $splitTableBorderWidth;

	var $bookmarkStyles;
	var $useActiveForms;

	var $repackageTTF;
	var $allowCJKorphans;
	var $allowCJKoverflow;

	var $useKerning;
	var $restrictColorSpace;
	var $bleedMargin;
	var $crossMarkMargin;
	var $cropMarkMargin;
	var $cropMarkLength;
	var $nonPrintMargin;

	var $PDFX;
	var $PDFXauto;

	var $PDFA;
	var $PDFAauto;
	var $ICCProfile;

	var $printers_info;
	var $iterationCounter;
	var $smCapsScale;
	var $smCapsStretch;

	var $backupSubsFont;
	var $backupSIPFont;
	var $debugfonts;
	var $useAdobeCJK;
	var $percentSubset;
	var $maxTTFFilesize;
	var $BMPonly;

	var $tableMinSizePriority;

	var $dpi;
	var $watermarkImgAlphaBlend;
	var $watermarkImgBehind;
	var $justifyB4br;
	var $packTableData;
	var $pgsIns;
	var $simpleTables;
	var $enableImports;

	var $debug;

	var $showStats;
	var $setAutoTopMargin;
	var $setAutoBottomMargin;
	var $autoMarginPadding;
	var $collapseBlockMargins;
	var $falseBoldWeight;
	var $normalLineheight;
	var $progressBar;
	var $incrementFPR1;
	var $incrementFPR2;
	var $incrementFPR3;
	var $incrementFPR4;

	var $SHYlang;
	var $SHYleftmin;
	var $SHYrightmin;
	var $SHYcharmin;
	var $SHYcharmax;
	var $SHYlanguages;

	// PageNumber Conditional Text
	var $pagenumPrefix;
	var $pagenumSuffix;

	var $nbpgPrefix;
	var $nbpgSuffix;
	var $showImageErrors;
	var $allow_output_buffering;
	var $autoPadding;
	var $useGraphs;
	var $tabSpaces;
	var $autoLangToFont;
	var $watermarkTextAlpha;
	var $watermarkImageAlpha;
	var $watermark_size;
	var $watermark_pos;
	var $annotSize;
	var $annotMargin;
	var $annotOpacity;
	var $title2annots;
	var $keepColumns;
	var $keep_table_proportions;
	var $ignore_table_widths;
	var $ignore_table_percents;
	var $list_number_suffix;

	var $list_auto_mode; // mPDF 6
	var $list_indent_first_level; // mPDF 6
	var $list_indent_default; // mPDF 6
	var $list_marker_offset; // mPDF 6

	var $useSubstitutions;
	var $CSSselectMedia;

	var $forcePortraitHeaders;
	var $forcePortraitMargins;
	var $displayDefaultOrientation;
	var $ignore_invalid_utf8;
	var $allowedCSStags;
	var $onlyCoreFonts;
	var $allow_charset_conversion;

	var $jSWord;
	var $jSmaxChar;
	var $jSmaxCharLast;
	var $jSmaxWordLast;

	var $max_colH_correction;

	var $table_error_report;
	var $table_error_report_param;
	var $biDirectional;
	var $text_input_as_HTML;
	var $anchor2Bookmark;
	var $shrink_tables_to_fit;

	var $allow_html_optional_endtags;

	var $img_dpi;

	var $defaultheaderfontsize;
	var $defaultheaderfontstyle;
	var $defaultheaderline;
	var $defaultfooterfontsize;
	var $defaultfooterfontstyle;
	var $defaultfooterline;
	var $header_line_spacing;
	var $footer_line_spacing;

	var $pregCJKchars;
	var $pregRTLchars;
	var $pregCURSchars; // mPDF 6

	var $mirrorMargins;
	var $watermarkText;
	var $watermarkImage;
	var $showWatermarkText;
	var $showWatermarkImage;

	var $fontsizes;

	var $defaultPageNumStyle; // mPDF 6

	//////////////////////
	// CLASS OBJECTS
	//////////////////////
	var $otl; // mPDF 5.7.1
	var $cssmgr;
	var $grad;
	var $bmp;
	var $wmf;
	var $tocontents;
	var $mpdfform;
	var $directw;

	//////////////////////
	// INTERNAL VARIABLES
	//////////////////////
	var $script2lang;
	var $viet;
	var $pashto;
	var $urdu;
	var $persian;
	var $sindhi;
	var $extrapagebreak; // mPDF 6 pagebreaktype

	var $uniqstr; // mPDF 5.7.2
	var $hasOC;

	var $textvar; // mPDF 5.7.1
	var $fontLanguageOverride; // mPDF 5.7.1
	var $OTLtags; // mPDF 5.7.1
	var $OTLdata;  // mPDF 5.7.1

	var $writingToC;
	var $layers;
	var $current_layer;
	var $open_layer_pane;
	var $decimal_offset;
	var $inMeter;

	var $CJKleading;
	var $CJKfollowing;
	var $CJKoverflow;

	var $textshadow;

	var $colsums;
	var $spanborder;
	var $spanborddet;

	var $visibility;

	var $useRC128encryption;
	var $uniqid;

	var $kerning;
	var $fixedlSpacing;
	var $minwSpacing;
	var $lSpacingCSS;
	var $wSpacingCSS;

	var $spotColorIDs;
	var $SVGcolors;
	var $spotColors;
	var $defTextColor;
	var $defDrawColor;
	var $defFillColor;

	var $tableBackgrounds;
	var $inlineDisplayOff;
	var $kt_y00;
	var $kt_p00;
	var $upperCase;
	var $checkSIP;
	var $checkSMP;
	var $checkCJK;

	var $watermarkImgAlpha;
	var $PDFAXwarnings;
	var $MetadataRoot;
	var $OutputIntentRoot;
	var $InfoRoot;
	var $current_filename;
	var $parsers;
	var $current_parser;
	var $_obj_stack;
	var $_don_obj_stack;
	var $_current_obj_id;
	var $tpls;
	var $tpl;
	var $tplprefix;
	var $_res;

	var $pdf_version;
	var $noImageFile;
	var $lastblockbottommargin;
	var $baselineC;

	// mPDF 5.7.3  inline text-decoration parameters
	var $baselineSup;
	var $baselineSub;
	var $baselineS;
	var $subPos;
	var $subArrMB;
	var $ReqFontStyle;
	var $tableClipPath;

	var $fullImageHeight;

	var $inFixedPosBlock;  // Internal flag for position:fixed block
	var $fixedPosBlock;  // Buffer string for position:fixed block
	var $fixedPosBlockDepth;
	var $fixedPosBlockBBox;
	var $fixedPosBlockSave;
	var $maxPosL;
	var $maxPosR;
	var $loaded;

	var $extraFontSubsets;

	var $docTemplateStart;  // Internal flag for page (page no. -1) that docTemplate starts on

	var $time0;

	// Classes
	var $indic;
	var $barcode;

	var $SHYpatterns;
	var $loadedSHYpatterns;
	var $loadedSHYdictionary;
	var $SHYdictionary;
	var $SHYdictionaryWords;

	var $spanbgcolorarray;
	var $default_font;
	var $headerbuffer;
	var $lastblocklevelchange;
	var $nestedtablejustfinished;
	var $linebreakjustfinished;
	var $cell_border_dominance_L;
	var $cell_border_dominance_R;
	var $cell_border_dominance_T;
	var $cell_border_dominance_B;
	var $table_keep_together;
	var $plainCell_properties;
	var $shrin_k1;
	var $outerfilled;

	var $blockContext;
	var $floatDivs;

	var $patterns;
	var $pageBackgrounds;

	var $bodyBackgroundGradient;
	var $bodyBackgroundImage;
	var $bodyBackgroundColor;

	var $writingHTMLheader; // internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
	var $writingHTMLfooter;

	var $angle;

	var $gradients;

	var $kwt_Reference;
	var $kwt_BMoutlines;
	var $kwt_toc;

	var $tbrot_BMoutlines;
	var $tbrot_toc;

	var $col_BMoutlines;
	var $col_toc;

	var $currentGraphId;
	var $graphs;

	var $floatbuffer;
	var $floatmargins;

	var $bullet;
	var $bulletarray;

	var $currentLang;
	var $default_lang;

	var $default_available_fonts;

	var $pageTemplate;
	var $docTemplate;
	var $docTemplateContinue;

	var $arabGlyphs;
	var $arabHex;
	var $persianGlyphs;
	var $persianHex;
	var $arabVowels;
	var $arabPrevLink;
	var $arabNextLink;

	var $formobjects; // array of Form Objects for WMF
	var $InlineProperties;
	var $InlineAnnots;
	var $InlineBDF; // mPDF 6 Bidirectional formatting
	var $InlineBDFctr; // mPDF 6

	var $ktAnnots;
	var $tbrot_Annots;
	var $kwt_Annots;
	var $columnAnnots;
	var $columnForms;

	var $PageAnnots;

	var $pageDim; // Keep track of page wxh for orientation changes - set in _beginpage, used in _putannots

	var $breakpoints;

	var $tableLevel;
	var $tbctr;
	var $innermostTableLevel;
	var $saveTableCounter;
	var $cellBorderBuffer;

	var $saveHTMLFooter_height;
	var $saveHTMLFooterE_height;

	var $firstPageBoxHeader;
	var $firstPageBoxHeaderEven;
	var $firstPageBoxFooter;
	var $firstPageBoxFooterEven;

	var $page_box;

	var $show_marks; // crop or cross marks
	var $basepathIsLocal;

	var $use_kwt;
	var $kwt;
	var $kwt_height;
	var $kwt_y0;
	var $kwt_x0;
	var $kwt_buffer;
	var $kwt_Links;
	var $kwt_moved;
	var $kwt_saved;

	var $PageNumSubstitutions;

	var $table_borders_separate;
	var $base_table_properties;
	var $borderstyles;

	var $blockjustfinished;

	var $orig_bMargin;
	var $orig_tMargin;
	var $orig_lMargin;
	var $orig_rMargin;
	var $orig_hMargin;
	var $orig_fMargin;

	var $pageHTMLheaders;
	var $pageHTMLfooters;

	var $saveHTMLHeader;
	var $saveHTMLFooter;

	var $HTMLheaderPageLinks;
	var $HTMLheaderPageAnnots;
	var $HTMLheaderPageForms;

	// See config_fonts.php for these next 5 values
	var $available_unifonts;
	var $sans_fonts;
	var $serif_fonts;
	var $mono_fonts;
	var $defaultSubsFont;

	// List of ALL available CJK fonts (incl. styles) (Adobe add-ons)  hw removed
	var $available_CJK_fonts;

	var $HTMLHeader;
	var $HTMLFooter;
	var $HTMLHeaderE;
	var $HTMLFooterE;
	var $bufferoutput;

	// CJK fonts
	var $Big5_widths;
	var $GB_widths;
	var $SJIS_widths;
	var $UHC_widths;

	// SetProtection
	var $encrypted; // whether document is protected
	var $Uvalue; // U entry in pdf document
	var $Ovalue; // O entry in pdf document
	var $Pvalue; // P entry in pdf document

	var $enc_obj_id; //encryption object id
	var $last_rc4_key; //last RC4 key encrypted (cached for optimisation)
	var $last_rc4_key_c; //last RC4 computed key

	var $encryption_key;

	var $padding; //used for encryption

	// Bookmark
	var $BMoutlines;
	var $OutlineRoot;

	// INDEX
	var $ColActive;
	var $Reference;
	var $CurrCol;
	var $NbCol;
	var $y0;   //Top ordinate of columns

	var $ColL;
	var $ColWidth;
	var $ColGap;

	// COLUMNS
	var $ColR;
	var $ChangeColumn;
	var $columnbuffer;
	var $ColDetails;
	var $columnLinks;
	var $colvAlign;

	// Substitutions
	var $substitute;  // Array of substitution strings e.g. <ttz>112</ttz>
	var $entsearch;  // Array of HTML entities (>ASCII 127) to substitute
	var $entsubstitute; // Array of substitution decimal unicode for the Hi entities

	// Default values if no style sheet offered	(cf. http://www.w3.org/TR/CSS21/sample.html)
	var $defaultCSS;
	var $lastoptionaltag; // Save current block item which HTML specifies optionsl endtag
	var $pageoutput;
	var $charset_in;
	var $blk;
	var $blklvl;
	var $ColumnAdjust;

	var $ws; // Word spacing

	var $HREF;
	var $pgwidth;
	var $fontlist;
	var $oldx;
	var $oldy;
	var $B;
	var $I;

	var $tdbegin;
	var $table;
	var $cell;
	var $col;
	var $row;

	var $divbegin;
	var $divwidth;
	var $divheight;
	var $spanbgcolor;

	// mPDF 6 Used for table cell (block-type) properties
	var $cellTextAlign;
	var $cellLineHeight;
	var $cellLineStackingStrategy;
	var $cellLineStackingShift;

	// mPDF 6  Lists
	var $listcounter;
	var $listlvl;
	var $listtype;
	var $listitem;

	var $pjustfinished;
	var $ignorefollowingspaces;
	var $SMALL;
	var $BIG;
	var $dash_on;
	var $dotted_on;

	var $textbuffer;
	var $currentfontstyle;
	var $currentfontfamily;
	var $currentfontsize;
	var $colorarray;
	var $bgcolorarray;
	var $internallink;
	var $enabledtags;

	var $lineheight;
	var $basepath;
	var $textparam;

	var $specialcontent;
	var $selectoption;
	var $objectbuffer;

	// Table Rotation
	var $table_rotate;
	var $tbrot_maxw;
	var $tbrot_maxh;
	var $tablebuffer;
	var $tbrot_align;
	var $tbrot_Links;

	var $keep_block_together; // Keep a Block from page-break-inside: avoid

	var $tbrot_y0;
	var $tbrot_x0;
	var $tbrot_w;
	var $tbrot_h;

	var $mb_enc;
	var $directionality;

	var $extgstates; // Used for alpha channel - Transparency (Watermark)
	var $mgl;
	var $mgt;
	var $mgr;
	var $mgb;

	var $tts;
	var $ttz;
	var $tta;

	// Best to alter the below variables using default stylesheet above
	var $page_break_after_avoid;
	var $margin_bottom_collapse;
	var $default_font_size; // in pts
	var $original_default_font_size; // used to save default sizes when using table default
	var $original_default_font;
	var $watermark_font;
	var $defaultAlign;

	// TABLE
	var $defaultTableAlign;
	var $tablethead;
	var $thead_font_weight;
	var $thead_font_style;
	var $thead_font_smCaps;
	var $thead_valign_default;
	var $thead_textalign_default;
	var $tabletfoot;
	var $tfoot_font_weight;
	var $tfoot_font_style;
	var $tfoot_font_smCaps;
	var $tfoot_valign_default;
	var $tfoot_textalign_default;

	var $trow_text_rotate;

	var $cellPaddingL;
	var $cellPaddingR;
	var $cellPaddingT;
	var $cellPaddingB;
	var $table_border_attr_set;
	var $table_border_css_set;

	var $shrin_k; // factor with which to shrink tables - used internally - do not change
	var $shrink_this_table_to_fit; // 0 or false to disable; value (if set) gives maximum factor to reduce fontsize
	var $MarginCorrection; // corrects for OddEven Margins
	var $margin_footer;
	var $margin_header;

	var $tabletheadjustfinished;
	var $usingCoreFont;
	var $charspacing;

	//Private properties FROM FPDF
	var $DisplayPreferences;
	var $flowingBlockAttr;
	var $page; //current page number
	var $n; //current object number
	var $offsets; //array of object offsets
	var $buffer; //buffer holding in-memory PDF
	var $pages; //array containing pages
	var $state; //current document state
	var $compress; //compression flag
	var $DefOrientation; //default orientation
	var $CurOrientation; //current orientation
	var $OrientationChanges; //array indicating orientation changes
	var $k; //scale factor (number of points in user unit)
	var $fwPt;
	var $fhPt; //dimensions of page format in points
	var $fw;
	var $fh; //dimensions of page format in user unit
	var $wPt;
	var $hPt; //current dimensions of page in points
	var $w;
	var $h; //current dimensions of page in user unit
	var $lMargin; //left margin
	var $tMargin; //top margin
	var $rMargin; //right margin
	var $bMargin; //page break margin
	var $cMarginL; //cell margin Left
	var $cMarginR; //cell margin Right
	var $cMarginT; //cell margin Left
	var $cMarginB; //cell margin Right
	var $DeflMargin; //Default left margin
	var $DefrMargin; //Default right margin
	var $x;
	var $y; //current position in user unit for cell positioning
	var $lasth; //height of last cell printed
	var $LineWidth; //line width in user unit
	var $CoreFonts; //array of standard font names
	var $fonts; //array of used fonts
	var $FontFiles; //array of font files
	var $images; //array of used images
	var $PageLinks; //array of links in pages
	var $links; //array of internal links
	var $FontFamily; //current font family
	var $FontStyle; //current font style
	var $CurrentFont; //current font info
	var $FontSizePt; //current font size in points
	var $FontSize; //current font size in user unit
	var $DrawColor; //commands for drawing color
	var $FillColor; //commands for filling color
	var $TextColor; //commands for text color
	var $ColorFlag; //indicates whether fill and text colors are different
	var $autoPageBreak; //automatic page breaking
	var $PageBreakTrigger; //threshold used to trigger page breaks
	var $InFooter; //flag set when processing footer

	var $InHTMLFooter;
	var $processingFooter; //flag set when processing footer - added for columns
	var $processingHeader; //flag set when processing header - added for columns
	var $ZoomMode; //zoom display mode
	var $LayoutMode; //layout display mode
	var $title; //title
	var $subject; //subject
	var $author; //author
	var $keywords; //keywords
	var $creator; //creator

	var $aliasNbPg; //alias for total number of pages
	var $aliasNbPgGp; //alias for total number of pages in page group

	//var $aliasNbPgHex;	// mPDF 6 deleted
	//var $aliasNbPgGpHex;	// mPDF 6 deleted

	var $ispre;
	var $outerblocktags;
	var $innerblocktags;

	// **********************************
	// **********************************
	// **********************************
	// **********************************
	// **********************************
	// **********************************
	// **********************************
	// **********************************
	// **********************************

	public function __construct($mode = '', $format = 'A4', $default_font_size = 0, $default_font = '', $mgl = 15, $mgr = 15, $mgt = 16, $mgb = 16, $mgh = 9, $mgf = 9, $orientation = 'P')
	{
		/* -- BACKGROUNDS -- */
		if (!class_exists('grad', false)) {
			include(_MPDF_PATH . 'classes/grad.php');
		}
		if (empty($this->grad)) {
			$this->grad = new grad($this);
		}
		/* -- END BACKGROUNDS -- */
		/* -- FORMS -- */
		if (!class_exists('mpdfform', false)) {
			include(_MPDF_PATH . 'classes/mpdfform.php');
		}
		if (empty($this->mpdfform)) {
			$this->mpdfform = new mpdfform($this);
		}
		/* -- END FORMS -- */

		$this->time0 = microtime(true);
		//Some checks
		$this->_dochecks();

		$this->writingToC = false;
		$this->layers = array();
		$this->current_layer = 0;
		$this->open_layer_pane = false;

		$this->visibility = 'visible';

		//Initialization of properties
		$this->spotColors = array();
		$this->spotColorIDs = array();
		$this->tableBackgrounds = array();
		$this->uniqstr = '20110230'; // mPDF 5.7.2
		$this->kt_y00 = '';
		$this->kt_p00 = '';
		$this->iterationCounter = false;
		$this->BMPonly = array();
		$this->page = 0;
		$this->n = 2;
		$this->buffer = '';
		$this->objectbuffer = array();
		$this->pages = array();
		$this->OrientationChanges = array();
		$this->state = 0;
		$this->fonts = array();
		$this->FontFiles = array();
		$this->images = array();
		$this->links = array();
		$this->InFooter = false;
		$this->processingFooter = false;
		$this->processingHeader = false;
		$this->lasth = 0;
		$this->FontFamily = '';
		$this->FontStyle = '';
		$this->FontSizePt = 9;
		$this->U = false;
		// Small Caps
		$this->upperCase = array();
		$this->smCapsScale = 1;
		$this->smCapsStretch = 100;
		$this->margBuffer = 0;
		$this->inMeter = false;
		$this->decimal_offset = 0;

		$this->defTextColor = $this->TextColor = $this->SetTColor($this->ConvertColor(0), true);
		$this->defDrawColor = $this->DrawColor = $this->SetDColor($this->ConvertColor(0), true);
		$this->defFillColor = $this->FillColor = $this->SetFColor($this->ConvertColor(255), true);

		//SVG color names array
		//http://www.w3schools.com/css/css_colornames.asp
		$this->SVGcolors = array('antiquewhite' => '#FAEBD7', 'aqua' => '#00FFFF', 'aquamarine' => '#7FFFD4', 'beige' => '#F5F5DC', 'black' => '#000000',
			'blue' => '#0000FF', 'brown' => '#A52A2A', 'cadetblue' => '#5F9EA0', 'chocolate' => '#D2691E', 'cornflowerblue' => '#6495ED', 'crimson' => '#DC143C',
			'darkblue' => '#00008B', 'darkgoldenrod' => '#B8860B', 'darkgreen' => '#006400', 'darkmagenta' => '#8B008B', 'darkorange' => '#FF8C00',
			'darkred' => '#8B0000', 'darkseagreen' => '#8FBC8F', 'darkslategray' => '#2F4F4F', 'darkviolet' => '#9400D3', 'deepskyblue' => '#00BFFF',
			'dodgerblue' => '#1E90FF', 'firebrick' => '#B22222', 'forestgreen' => '#228B22', 'fuchsia' => '#FF00FF', 'gainsboro' => '#DCDCDC', 'gold' => '#FFD700',
			'gray' => '#808080', 'green' => '#008000', 'greenyellow' => '#ADFF2F', 'hotpink' => '#FF69B4', 'indigo' => '#4B0082', 'khaki' => '#F0E68C',
			'lavenderblush' => '#FFF0F5', 'lemonchiffon' => '#FFFACD', 'lightcoral' => '#F08080', 'lightgoldenrodyellow' => '#FAFAD2', 'lightgreen' => '#90EE90',
			'lightsalmon' => '#FFA07A', 'lightskyblue' => '#87CEFA', 'lightslategray' => '#778899', 'lightyellow' => '#FFFFE0', 'lime' => '#00FF00', 'limegreen' => '#32CD32',
			'magenta' => '#FF00FF', 'maroon' => '#800000', 'mediumaquamarine' => '#66CDAA', 'mediumorchid' => '#BA55D3', 'mediumseagreen' => '#3CB371',
			'mediumspringgreen' => '#00FA9A', 'mediumvioletred' => '#C71585', 'midnightblue' => '#191970', 'mintcream' => '#F5FFFA', 'moccasin' => '#FFE4B5', 'navy' => '#000080',
			'olive' => '#808000', 'orange' => '#FFA500', 'orchid' => '#DA70D6', 'palegreen' => '#98FB98',
			'palevioletred' => '#D87093', 'peachpuff' => '#FFDAB9', 'pink' => '#FFC0CB', 'powderblue' => '#B0E0E6', 'purple' => '#800080',
			'red' => '#FF0000', 'royalblue' => '#4169E1', 'salmon' => '#FA8072', 'seagreen' => '#2E8B57', 'sienna' => '#A0522D', 'silver' => '#C0C0C0', 'skyblue' => '#87CEEB',
			'slategray' => '#708090', 'springgreen' => '#00FF7F', 'steelblue' => '#4682B4', 'tan' => '#D2B48C', 'teal' => '#008080', 'thistle' => '#D8BFD8', 'turquoise' => '#40E0D0',
			'violetred' => '#D02090', 'white' => '#FFFFFF', 'yellow' => '#FFFF00',
			'aliceblue' => '#f0f8ff', 'azure' => '#f0ffff', 'bisque' => '#ffe4c4', 'blanchedalmond' => '#ffebcd', 'blueviolet' => '#8a2be2', 'burlywood' => '#deb887',
			'chartreuse' => '#7fff00', 'coral' => '#ff7f50', 'cornsilk' => '#fff8dc', 'cyan' => '#00ffff', 'darkcyan' => '#008b8b', 'darkgray' => '#a9a9a9',
			'darkgrey' => '#a9a9a9', 'darkkhaki' => '#bdb76b', 'darkolivegreen' => '#556b2f', 'darkorchid' => '#9932cc', 'darksalmon' => '#e9967a',
			'darkslateblue' => '#483d8b', 'darkslategrey' => '#2f4f4f', 'darkturquoise' => '#00ced1', 'deeppink' => '#ff1493', 'dimgray' => '#696969',
			'dimgrey' => '#696969', 'floralwhite' => '#fffaf0', 'ghostwhite' => '#f8f8ff', 'goldenrod' => '#daa520', 'grey' => '#808080', 'honeydew' => '#f0fff0',
			'indianred' => '#cd5c5c', 'ivory' => '#fffff0', 'lavender' => '#e6e6fa', 'lawngreen' => '#7cfc00', 'lightblue' => '#add8e6', 'lightcyan' => '#e0ffff',
			'lightgray' => '#d3d3d3', 'lightgrey' => '#d3d3d3', 'lightpink' => '#ffb6c1', 'lightseagreen' => '#20b2aa', 'lightslategrey' => '#778899',
			'lightsteelblue' => '#b0c4de', 'linen' => '#faf0e6', 'mediumblue' => '#0000cd', 'mediumpurple' => '#9370db', 'mediumslateblue' => '#7b68ee',
			'mediumturquoise' => '#48d1cc', 'mistyrose' => '#ffe4e1', 'navajowhite' => '#ffdead', 'oldlace' => '#fdf5e6', 'olivedrab' => '#6b8e23', 'orangered' => '#ff4500',
			'palegoldenrod' => '#eee8aa', 'paleturquoise' => '#afeeee', 'papayawhip' => '#ffefd5', 'peru' => '#cd853f', 'plum' => '#dda0dd', 'rosybrown' => '#bc8f8f',
			'saddlebrown' => '#8b4513', 'sandybrown' => '#f4a460', 'seashell' => '#fff5ee', 'slateblue' => '#6a5acd', 'slategrey' => '#708090', 'snow' => '#fffafa',
			'tomato' => '#ff6347', 'violet' => '#ee82ee', 'wheat' => '#f5deb3', 'whitesmoke' => '#f5f5f5', 'yellowgreen' => '#9acd32');

		// Uppercase alternatives (for Small Caps)
		if (empty($this->upperCase)) {
			@include(_MPDF_PATH . 'includes/upperCase.php');
		}
		$this->extrapagebreak = true; // mPDF 6 pagebreaktype

		$this->ColorFlag = false;
		$this->extgstates = array();

		$this->mb_enc = 'windows-1252';
		$this->directionality = 'ltr';
		$this->defaultAlign = 'L';
		$this->defaultTableAlign = 'L';

		$this->fixedPosBlockSave = array();
		$this->extraFontSubsets = 0;

		$this->SHYpatterns = array();
		$this->loadedSHYdictionary = false;
		$this->SHYdictionary = array();
		$this->SHYdictionaryWords = array();
		$this->blockContext = 1;
		$this->floatDivs = array();
		$this->DisplayPreferences = '';

		$this->patterns = array();  // Tiling patterns used for backgrounds
		$this->pageBackgrounds = array();
		$this->writingHTMLheader = false; // internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
		$this->writingHTMLfooter = false; // internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
		$this->gradients = array();

		$this->kwt_Reference = array();
		$this->kwt_BMoutlines = array();
		$this->kwt_toc = array();

		$this->tbrot_BMoutlines = array();
		$this->tbrot_toc = array();

		$this->col_BMoutlines = array();
		$this->col_toc = array();
		$this->graphs = array();

		$this->pgsIns = array();
		$this->PDFAXwarnings = array();
		$this->inlineDisplayOff = false;
		$this->lSpacingCSS = '';
		$this->wSpacingCSS = '';
		$this->fixedlSpacing = false;
		$this->minwSpacing = 0;


		$this->baselineC = 0.35; // Baseline for text
		// mPDF 5.7.3  inline text-decoration parameters
		$this->baselineSup = 0.5; // Sets default change in baseline for <sup> text as factor of preceeding fontsize
		// 0.35 has been recommended; 0.5 matches applications like MS Word
		$this->baselineSub = -0.2; // Sets default change in baseline for <sub> text as factor of preceeding fontsize
		$this->baselineS = 0.3;  // Sets default height for <strike> text as factor of fontsize
		$this->baselineO = 1.1;  // Sets default height for overline text as factor of fontsize

		$this->noImageFile = str_replace("\\", "/", dirname(__FILE__)) . '/includes/no_image.jpg';
		$this->subPos = 0;
		$this->normalLineheight = 1.3; // This should be overridden in config.php - but it is so important a default value is put here
		// These are intended as configuration variables, and should be set in config.php - which will override these values;
		// set here as failsafe as will cause an error if not defined
		$this->incrementFPR1 = 10;
		$this->incrementFPR2 = 10;
		$this->incrementFPR3 = 10;
		$this->incrementFPR4 = 10;

		$this->fullImageHeight = false;
		$this->floatbuffer = array();
		$this->floatmargins = array();
		$this->formobjects = array(); // array of Form Objects for WMF
		$this->InlineProperties = array();
		$this->InlineAnnots = array();
		$this->InlineBDF = array(); // mPDF 6
		$this->InlineBDFctr = 0; // mPDF 6
		$this->tbrot_Annots = array();
		$this->kwt_Annots = array();
		$this->columnAnnots = array();
		$this->pageDim = array();
		$this->breakpoints = array(); // used in columnbuffer
		$this->tableLevel = 0;
		$this->tbctr = array(); // counter for nested tables at each level
		$this->page_box = array();
		$this->show_marks = ''; // crop or cross marks
		$this->kwt = false;
		$this->kwt_height = 0;
		$this->kwt_y0 = 0;
		$this->kwt_x0 = 0;
		$this->kwt_buffer = array();
		$this->kwt_Links = array();
		$this->kwt_moved = false;
		$this->kwt_saved = false;
		$this->PageNumSubstitutions = array();
		$this->base_table_properties = array();
		$this->borderstyles = array('inset', 'groove', 'outset', 'ridge', 'dotted', 'dashed', 'solid', 'double');
		$this->tbrot_align = 'C';

		$this->pageHTMLheaders = array();
		$this->pageHTMLfooters = array();
		$this->HTMLheaderPageLinks = array();
		$this->HTMLheaderPageAnnots = array();

		$this->HTMLheaderPageForms = array();
		$this->columnForms = array();
		$this->tbrotForms = array();
		$this->useRC128encryption = false;
		$this->uniqid = '';

		$this->pageoutput = array();

		$this->bufferoutput = false;
		$this->encrypted = false;      //whether document is protected
		$this->BMoutlines = array();
		$this->ColActive = 0;          //Flag indicating that columns are on (the index is being processed)
		$this->Reference = array();    //Array containing the references
		$this->CurrCol = 0;               //Current column number
		$this->ColL = array(0);   // Array of Left pos of columns - absolute - needs Margin correction for Odd-Even
		$this->ColR = array(0);   // Array of Right pos of columns - absolute pos - needs Margin correction for Odd-Even
		$this->ChangeColumn = 0;
		$this->columnbuffer = array();
		$this->ColDetails = array();  // Keeps track of some column details
		$this->columnLinks = array();  // Cross references PageLinks
		$this->substitute = array();  // Array of substitution strings e.g. <ttz>112</ttz>
		$this->entsearch = array();  // Array of HTML entities (>ASCII 127) to substitute
		$this->entsubstitute = array(); // Array of substitution decimal unicode for the Hi entities
		$this->lastoptionaltag = '';
		$this->charset_in = '';
		$this->blk = array();
		$this->blklvl = 0;
		$this->tts = false;
		$this->ttz = false;
		$this->tta = false;
		$this->ispre = false;

		$this->checkSIP = false;
		$this->checkSMP = false;
		$this->checkCJK = false;

		$this->page_break_after_avoid = false;
		$this->margin_bottom_collapse = false;
		$this->tablethead = 0;
		$this->tabletfoot = 0;
		$this->table_border_attr_set = 0;
		$this->table_border_css_set = 0;
		$this->shrin_k = 1.0;
		$this->shrink_this_table_to_fit = 0;
		$this->MarginCorrection = 0;

		$this->tabletheadjustfinished = false;
		$this->usingCoreFont = false;
		$this->charspacing = 0;

		$this->autoPageBreak = true;

		require(_MPDF_PATH . 'config.php'); // config data

		$this->_setPageSize($format, $orientation);
		$this->DefOrientation = $orientation;

		$this->margin_header = $mgh;
		$this->margin_footer = $mgf;

		$bmargin = $mgb;

		$this->DeflMargin = $mgl;
		$this->DefrMargin = $mgr;

		$this->orig_tMargin = $mgt;
		$this->orig_bMargin = $bmargin;
		$this->orig_lMargin = $this->DeflMargin;
		$this->orig_rMargin = $this->DefrMargin;
		$this->orig_hMargin = $this->margin_header;
		$this->orig_fMargin = $this->margin_footer;

		if ($this->setAutoTopMargin == 'pad') {
			$mgt += $this->margin_header;
		}
		if ($this->setAutoBottomMargin == 'pad') {
			$mgb += $this->margin_footer;
		}
		$this->SetMargins($this->DeflMargin, $this->DefrMargin, $mgt); // sets l r t margin
		//Automatic page break
		$this->SetAutoPageBreak($this->autoPageBreak, $bmargin); // sets $this->bMargin & PageBreakTrigger

		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;

		//Interior cell margin (1 mm) ? not used
		$this->cMarginL = 1;
		$this->cMarginR = 1;
		//Line width (0.2 mm)
		$this->LineWidth = .567 / _MPDFK;

		//To make the function Footer() work - replaces {nb} with page number
		$this->AliasNbPages();
		$this->AliasNbPageGroups();

		//$this->aliasNbPgHex = '{nbHEXmarker}';	// mPDF 6 deleted
		//$this->aliasNbPgGpHex = '{nbpgHEXmarker}';	// mPDF 6 deleted
		//Enable all tags as default
		$this->DisableTags();
		//Full width display mode
		$this->SetDisplayMode(100); // fullwidth?		'fullpage'
		//Compression
		$this->SetCompression(true);
		//Set default display preferences
		$this->SetDisplayPreferences('');

		// Font data
		require(_MPDF_PATH . 'config_fonts.php');
		// Available fonts
		$this->available_unifonts = array();
		foreach ($this->fontdata AS $f => $fs) {
			if (isset($fs['R']) && $fs['R']) {
				$this->available_unifonts[] = $f;
			}
			if (isset($fs['B']) && $fs['B']) {
				$this->available_unifonts[] = $f . 'B';
			}
			if (isset($fs['I']) && $fs['I']) {
				$this->available_unifonts[] = $f . 'I';
			}
			if (isset($fs['BI']) && $fs['BI']) {
				$this->available_unifonts[] = $f . 'BI';
			}
		}

		$this->default_available_fonts = $this->available_unifonts;

		$optcore = false;
		$onlyCoreFonts = false;
		if (preg_match('/([\-+])aCJK/i', $mode, $m)) {
			$mode = preg_replace('/([\-+])aCJK/i', '', $mode); // mPDF 6
			if ($m[1] == '+') {
				$this->useAdobeCJK = true;
			} else {
				$this->useAdobeCJK = false;
			}
		}

		if (strlen($mode) == 1) {
			if ($mode == 's') {
				$this->percentSubset = 100;
				$mode = '';
			} else if ($mode == 'c') {
				$onlyCoreFonts = true;
				$mode = '';
			}
		} else if (substr($mode, -2) == '-s') {
			$this->percentSubset = 100;
			$mode = substr($mode, 0, strlen($mode) - 2);
		} else if (substr($mode, -2) == '-c') {
			$onlyCoreFonts = true;
			$mode = substr($mode, 0, strlen($mode) - 2);
		} else if (substr($mode, -2) == '-x') {
			$optcore = true;
			$mode = substr($mode, 0, strlen($mode) - 2);
		}

		// Autodetect if mode is a language_country string (en-GB or en_GB or en)
		if ($mode && $mode != 'UTF-8') { // mPDF 6
			list ($coreSuitable, $mpdf_pdf_unifont) = GetLangOpts($mode, $this->useAdobeCJK, $this->fontdata);
			if ($coreSuitable && $optcore) {
				$onlyCoreFonts = true;
			}
			if ($mpdf_pdf_unifont) {  // mPDF 6
				$default_font = $mpdf_pdf_unifont;
			}
			$this->currentLang = $mode;
			$this->default_lang = $mode;
		}

		$this->onlyCoreFonts = $onlyCoreFonts;

		if ($this->onlyCoreFonts) {
			$this->setMBencoding('windows-1252'); // sets $this->mb_enc
		} else {
			$this->setMBencoding('UTF-8'); // sets $this->mb_enc
		}
		@mb_regex_encoding('UTF-8'); // required only for mb_ereg... and mb_split functions
		// Adobe CJK fonts
		$this->available_CJK_fonts = array('gb', 'big5', 'sjis', 'uhc', 'gbB', 'big5B', 'sjisB', 'uhcB', 'gbI', 'big5I', 'sjisI', 'uhcI',
			'gbBI', 'big5BI', 'sjisBI', 'uhcBI');


		//Standard fonts
		$this->CoreFonts = array('ccourier' => 'Courier', 'ccourierB' => 'Courier-Bold', 'ccourierI' => 'Courier-Oblique', 'ccourierBI' => 'Courier-BoldOblique',
			'chelvetica' => 'Helvetica', 'chelveticaB' => 'Helvetica-Bold', 'chelveticaI' => 'Helvetica-Oblique', 'chelveticaBI' => 'Helvetica-BoldOblique',
			'ctimes' => 'Times-Roman', 'ctimesB' => 'Times-Bold', 'ctimesI' => 'Times-Italic', 'ctimesBI' => 'Times-BoldItalic',
			'csymbol' => 'Symbol', 'czapfdingbats' => 'ZapfDingbats');
		$this->fontlist = array("ctimes", "ccourier", "chelvetica", "csymbol", "czapfdingbats");

		// Substitutions
		$this->setHiEntitySubstitutions();

		if ($this->onlyCoreFonts) {
			$this->useSubstitutions = true;
			$this->SetSubstitutions();
		} else {
			$this->useSubstitutions = false;
		}

		/* -- HTML-CSS -- */

		if (!class_exists('cssmgr', false)) {
			include(_MPDF_PATH . 'classes/cssmgr.php');
		}
		$this->cssmgr = new cssmgr($this);
		// mPDF 6
		if (file_exists(_MPDF_PATH . 'mpdf.css')) {
			$css = file_get_contents(_MPDF_PATH . 'mpdf.css');
			$this->cssmgr->ReadCSS('<style> ' . $css . ' </style>');
		}
		/* -- END HTML-CSS -- */

		if ($default_font == '') {
			if ($this->onlyCoreFonts) {
				if (in_array(strtolower($this->defaultCSS['BODY']['FONT-FAMILY']), $this->mono_fonts)) {
					$default_font = 'ccourier';
				} else if (in_array(strtolower($this->defaultCSS['BODY']['FONT-FAMILY']), $this->sans_fonts)) {
					$default_font = 'chelvetica';
				} else {
					$default_font = 'ctimes';
				}
			} else {
				$default_font = $this->defaultCSS['BODY']['FONT-FAMILY'];
			}
		}
		if (!$default_font_size) {
			$mmsize = $this->ConvertSize($this->defaultCSS['BODY']['FONT-SIZE']);
			$default_font_size = $mmsize * (_MPDFK);
		}

		if ($default_font) {
			$this->SetDefaultFont($default_font);
		}
		if ($default_font_size) {
			$this->SetDefaultFontSize($default_font_size);
		}

		$this->SetLineHeight(); // lineheight is in mm

		$this->SetFColor($this->ConvertColor(255));
		$this->HREF = '';
		$this->oldy = -1;
		$this->B = 0;
		$this->I = 0;

		// mPDF 6  Lists
		$this->listlvl = 0;
		$this->listtype = array();
		$this->listitem = array();
		$this->listcounter = array();

		$this->tdbegin = false;
		$this->table = array();
		$this->cell = array();
		$this->col = -1;
		$this->row = -1;
		$this->cellBorderBuffer = array();

		$this->divbegin = false;
		// mPDF 6
		$this->cellTextAlign = '';
		$this->cellLineHeight = '';
		$this->cellLineStackingStrategy = '';
		$this->cellLineStackingShift = '';

		$this->divwidth = 0;
		$this->divheight = 0;
		$this->spanbgcolor = false;
		$this->spanborder = false;
		$this->spanborddet = array();

		$this->blockjustfinished = false;
		$this->ignorefollowingspaces = true; //in order to eliminate exceeding left-side spaces
		$this->dash_on = false;
		$this->dotted_on = false;
		$this->textshadow = '';

		$this->currentfontfamily = '';
		$this->currentfontsize = '';
		$this->currentfontstyle = '';
		$this->colorarray = ''; // mPDF 6
		$this->spanbgcolorarray = ''; // mPDF 6
		$this->textbuffer = array();
		$this->internallink = array();
		$this->basepath = "";

		$this->SetBasePath('');

		$this->textparam = array();

		$this->specialcontent = '';
		$this->selectoption = array();

		/* -- IMPORTS -- */

		$this->tpls = array();
		$this->tpl = 0;
		$this->tplprefix = "/TPL";
		$this->res = array();
		if ($this->enableImports) {
			$this->SetImportUse();
		}
		/* -- END IMPORTS -- */

		if ($this->progressBar) {
			$this->StartProgressBarOutput($this->progressBar);
		} // *PROGRESS-BAR*
	}

	function _setPageSize($format, &$orientation)
	{
		//Page format
		if (is_string($format)) {
			if ($format == '') {
				$format = 'A4';
			}
			$pfo = 'P';
			if (preg_match('/([0-9a-zA-Z]*)-L/i', $format, $m)) { // e.g. A4-L = A4 landscape
				$format = $m[1];
				$pfo = 'L';
			}
			$format = $this->_getPageFormat($format);
			if (!$format) {
				$this->Error('Unknown page format: ' . $format);
			} else {
				$orientation = $pfo;
			}

			$this->fwPt = $format[0];
			$this->fhPt = $format[1];
		} else {
			if (!$format[0] || !$format[1]) {
				$this->Error('Invalid page format: ' . $format[0] . ' ' . $format[1]);
			}
			$this->fwPt = $format[0] * _MPDFK;
			$this->fhPt = $format[1] * _MPDFK;
		}
		$this->fw = $this->fwPt / _MPDFK;
		$this->fh = $this->fhPt / _MPDFK;
		//Page orientation
		$orientation = strtolower($orientation);
		if ($orientation == 'p' or $orientation == 'portrait') {
			$orientation = 'P';
			$this->wPt = $this->fwPt;
			$this->hPt = $this->fhPt;
		} elseif ($orientation == 'l' or $orientation == 'landscape') {
			$orientation = 'L';
			$this->wPt = $this->fhPt;
			$this->hPt = $this->fwPt;
		} else
			$this->Error('Incorrect orientation: ' . $orientation);
		$this->CurOrientation = $orientation;

		$this->w = $this->wPt / _MPDFK;
		$this->h = $this->hPt / _MPDFK;
	}

	function _getPageFormat($format)
	{
		switch (strtoupper($format)) {
			case '4A0': {
					$format = array(4767.87, 6740.79);
					break;
				}
			case '2A0': {
					$format = array(3370.39, 4767.87);
					break;
				}
			case 'A0': {
					$format = array(2383.94, 3370.39);
					break;
				}
			case 'A1': {
					$format = array(1683.78, 2383.94);
					break;
				}
			case 'A2': {
					$format = array(1190.55, 1683.78);
					break;
				}
			case 'A3': {
					$format = array(841.89, 1190.55);
					break;
				}
			case 'A4': {
					$format = array(595.28, 841.89);
					break;
				}
			case 'A5': {
					$format = array(419.53, 595.28);
					break;
				}
			case 'A6': {
					$format = array(297.64, 419.53);
					break;
				}
			case 'A7': {
					$format = array(209.76, 297.64);
					break;
				}
			case 'A8': {
					$format = array(147.40, 209.76);
					break;
				}
			case 'A9': {
					$format = array(104.88, 147.40);
					break;
				}
			case 'A10': {
					$format = array(73.70, 104.88);
					break;
				}
			case 'B0': {
					$format = array(2834.65, 4008.19);
					break;
				}
			case 'B1': {
					$format = array(2004.09, 2834.65);
					break;
				}
			case 'B2': {
					$format = array(1417.32, 2004.09);
					break;
				}
			case 'B3': {
					$format = array(1000.63, 1417.32);
					break;
				}
			case 'B4': {
					$format = array(708.66, 1000.63);
					break;
				}
			case 'B5': {
					$format = array(498.90, 708.66);
					break;
				}
			case 'B6': {
					$format = array(354.33, 498.90);
					break;
				}
			case 'B7': {
					$format = array(249.45, 354.33);
					break;
				}
			case 'B8': {
					$format = array(175.75, 249.45);
					break;
				}
			case 'B9': {
					$format = array(124.72, 175.75);
					break;
				}
			case 'B10': {
					$format = array(87.87, 124.72);
					break;
				}
			case 'C0': {
					$format = array(2599.37, 3676.54);
					break;
				}
			case 'C1': {
					$format = array(1836.85, 2599.37);
					break;
				}
			case 'C2': {
					$format = array(1298.27, 1836.85);
					break;
				}
			case 'C3': {
					$format = array(918.43, 1298.27);
					break;
				}
			case 'C4': {
					$format = array(649.13, 918.43);
					break;
				}
			case 'C5': {
					$format = array(459.21, 649.13);
					break;
				}
			case 'C6': {
					$format = array(323.15, 459.21);
					break;
				}
			case 'C7': {
					$format = array(229.61, 323.15);
					break;
				}
			case 'C8': {
					$format = array(161.57, 229.61);
					break;
				}
			case 'C9': {
					$format = array(113.39, 161.57);
					break;
				}
			case 'C10': {
					$format = array(79.37, 113.39);
					break;
				}
			case 'RA0': {
					$format = array(2437.80, 3458.27);
					break;
				}
			case 'RA1': {
					$format = array(1729.13, 2437.80);
					break;
				}
			case 'RA2': {
					$format = array(1218.90, 1729.13);
					break;
				}
			case 'RA3': {
					$format = array(864.57, 1218.90);
					break;
				}
			case 'RA4': {
					$format = array(609.45, 864.57);
					break;
				}
			case 'SRA0': {
					$format = array(2551.18, 3628.35);
					break;
				}
			case 'SRA1': {
					$format = array(1814.17, 2551.18);
					break;
				}
			case 'SRA2': {
					$format = array(1275.59, 1814.17);
					break;
				}
			case 'SRA3': {
					$format = array(907.09, 1275.59);
					break;
				}
			case 'SRA4': {
					$format = array(637.80, 907.09);
					break;
				}
			case 'LETTER': {
					$format = array(612.00, 792.00);
					break;
				}
			case 'LEGAL': {
					$format = array(612.00, 1008.00);
					break;
				}
			case 'LEDGER': {
					$format = array(1224.00, 792.00);
					break;
				}
			case 'TABLOID': {
					$format = array(792.00, 1224.00);
					break;
				}
			case 'EXECUTIVE': {
					$format = array(521.86, 756.00);
					break;
				}
			case 'FOLIO': {
					$format = array(612.00, 936.00);
					break;
				}
			case 'B': {
					$format = array(362.83, 561.26);
					break;
				}  //	'B' format paperback size 128x198mm
			case 'A': {
					$format = array(314.65, 504.57);
					break;
				}  //	'A' format paperback size 111x178mm
			case 'DEMY': {
					$format = array(382.68, 612.28);
					break;
				}  //	'Demy' format paperback size 135x216mm
			case 'ROYAL': {
					$format = array(433.70, 663.30);
					break;
				} //	'Royal' format paperback size 153x234mm
			default: {
					$format = array(595.28, 841.89);
					break;
				}
		}
		return $format;
	}

	

	function RestrictUnicodeFonts($res)
	{
		// $res = array of (Unicode) fonts to restrict to: e.g. norasi|norasiB - language specific
		if (count($res)) { // Leave full list of available fonts if passed blank array
			$this->available_unifonts = $res;
		} else {
			$this->available_unifonts = $this->default_available_fonts;
		}
		if (count($this->available_unifonts) == 0) {
			$this->available_unifonts[] = $this->default_available_fonts[0];
		}
		$this->available_unifonts = array_values($this->available_unifonts);
	}

	function setMBencoding($enc)
	{
		if ($this->mb_enc != $enc) {
			$this->mb_enc = $enc;
			mb_internal_encoding($this->mb_enc);
		}
	}

	function SetMargins($left, $right, $top)
	{
		//Set left, top and right margins
		$this->lMargin = $left;
		$this->rMargin = $right;
		$this->tMargin = $top;
	}

	function ResetMargins()
	{
		//ReSet left, top margins
		if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation == 'P' && $this->CurOrientation == 'L') {
			if (($this->mirrorMargins) && (($this->page) % 2 == 0)) { // EVEN
				$this->tMargin = $this->orig_rMargin;
				$this->bMargin = $this->orig_lMargin;
			} else { // ODD	// OR NOT MIRRORING MARGINS/FOOTERS
				$this->tMargin = $this->orig_lMargin;
				$this->bMargin = $this->orig_rMargin;
			}
			$this->lMargin = $this->DeflMargin;
			$this->rMargin = $this->DefrMargin;
			$this->MarginCorrection = 0;
			$this->PageBreakTrigger = $this->h - $this->bMargin;
		} else if (($this->mirrorMargins) && (($this->page) % 2 == 0)) { // EVEN
			$this->lMargin = $this->DefrMargin;
			$this->rMargin = $this->DeflMargin;
			$this->MarginCorrection = $this->DefrMargin - $this->DeflMargin;
		} else { // ODD	// OR NOT MIRRORING MARGINS/FOOTERS
			$this->lMargin = $this->DeflMargin;
			$this->rMargin = $this->DefrMargin;
			if ($this->mirrorMargins) {
				$this->MarginCorrection = $this->DeflMargin - $this->DefrMargin;
			}
		}
		$this->x = $this->lMargin;
	}

	function SetLeftMargin($margin)
	{
		//Set left margin
		$this->lMargin = $margin;
		if ($this->page > 0 and $this->x < $margin)
			$this->x = $margin;
	}

	function SetTopMargin($margin)
	{
		//Set top margin
		$this->tMargin = $margin;
	}

	function SetRightMargin($margin)
	{
		//Set right margin
		$this->rMargin = $margin;
	}

	function SetAutoPageBreak($auto, $margin = 0)
	{
		//Set auto page break mode and triggering margin
		$this->autoPageBreak = $auto;
		$this->bMargin = $margin;
		$this->PageBreakTrigger = $this->h - $margin;
	}

	function SetDisplayMode($zoom, $layout = 'continuous')
	{
		//Set display mode in viewer
		if ($zoom == 'fullpage' or $zoom == 'fullwidth' or $zoom == 'real' or $zoom == 'default' or ! is_string($zoom))
			$this->ZoomMode = $zoom;
		else
			$this->Error('Incorrect zoom display mode: ' . $zoom);
		if ($layout == 'single' or $layout == 'continuous' or $layout == 'two' or $layout == 'twoleft' or $layout == 'tworight' or $layout == 'default')
			$this->LayoutMode = $layout;
		else
			$this->Error('Incorrect layout display mode: ' . $layout);
	}

	function SetCompression($compress)
	{
		//Set page compression
		if (function_exists('gzcompress'))
			$this->compress = $compress;
		else
			$this->compress = false;
	}

	function SetTitle($title)
	{
		//Title of document // Arrives as UTF-8
		$this->title = $title;
	}

	function SetSubject($subject)
	{
		//Subject of document
		$this->subject = $subject;
	}

	function SetAuthor($author)
	{
		//Author of document
		$this->author = $author;
	}

	function SetKeywords($keywords)
	{
		//Keywords of document
		$this->keywords = $keywords;
	}

	function SetCreator($creator)
	{
		//Creator of document
		$this->creator = $creator;
	}

	function SetAnchor2Bookmark($x)
	{
		$this->anchor2Bookmark = $x;
	}

	function AliasNbPages($alias = '{nb}')
	{
		//Define an alias for total number of pages
		$this->aliasNbPg = $alias;
	}

	function AliasNbPageGroups($alias = '{nbpg}')
	{
		//Define an alias for total number of pages in a group
		$this->aliasNbPgGp = $alias;
	}

	function SetAlpha($alpha, $bm = 'Normal', $return = false, $mode = 'B')
	{
// alpha: real value from 0 (transparent) to 1 (opaque)
// bm:    blend mode, one of the following:
//          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
//          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
// set alpha for stroking (CA) and non-stroking (ca) operations
// mode determines F (fill) S (stroke) B (both)
		if (($this->PDFA || $this->PDFX) && $alpha != 1) {
			if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) {
				$this->PDFAXwarnings[] = "Image opacity must be 100% (Opacity changed to 100%)";
			}
			$alpha = 1;
		}
		$a = array('BM' => '/' . $bm);
		if ($mode == 'F' || $mode == 'B')
			$a['ca'] = $alpha; // mPDF 5.7.2
		if ($mode == 'S' || $mode == 'B')
			$a['CA'] = $alpha; // mPDF 5.7.2
		$gs = $this->AddExtGState($a);
		if ($return) {
			return sprintf('/GS%d gs', $gs);
		} else {
			$this->_out(sprintf('/GS%d gs', $gs));
		}
	}

	function AddExtGState($parms)
	{
		$n = count($this->extgstates);
		// check if graphics state already exists
		for ($i = 1; $i <= $n; $i++) {
			if (count($this->extgstates[$i]['parms']) == count($parms)) {
				$same = true;
				foreach ($this->extgstates[$i]['parms'] AS $k => $v) {
					if (!isset($parms[$k]) || $parms[$k] != $v) {
						$same = false;
						break;
					}
				}
				if ($same) {
					return $i;
				}
			}
		}
		$n++;
		$this->extgstates[$n]['parms'] = $parms;
		return $n;
	}

	
	function Error($msg)
	{
		//Fatal error
		header('Content-Type: text/html; charset=utf-8');
		die('<B>mPDF error: </B>' . $msg);
	}

	function Open()
	{
		//Begin document
		if ($this->state == 0) {
			// Was is function _begindoc()
			// Start document
			$this->state = 1;
			$this->_out('%PDF-' . $this->pdf_version);
			$this->_out('%' . chr(226) . chr(227) . chr(207) . chr(211)); // 4 chars > 128 to show binary file
		}
	}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// DEPRACATED but included for backwards compatability
// Depracated - can use AddPage for all
	function AddPages($a = '', $b = '', $c = '', $d = '', $e = '', $f = '', $g = '', $h = '', $i = '', $j = '', $k = '', $l = '', $m = '', $n = '', $o = '', $p = 0, $q = 0, $r = 0, $s = 0, $t = '', $u = '')
	{
		$this->Error('function AddPages is depracated as of mPDF 6. Please use AddPage or HTML code methods instead.');
	}

	function startPageNums()
	{
		$this->Error('function startPageNums is depracated as of mPDF 6.');
	}

	function setUnvalidatedText($a = '', $b = -1)
	{
		$this->Error('function setUnvalidatedText is depracated as of mPDF 6. Please use SetWatermarkText instead.');
	}

	function SetAutoFont($a)
	{
		$this->Error('function SetAutoFont is depracated as of mPDF 6. Please use autoScriptToLang instead. See config.php');
	}

	function Reference($a)
	{
		$this->Error('function Reference is depracated as of mPDF 6. Please use IndexEntry instead.');
	}

	function ReferenceSee($a, $b)
	{
		$this->Error('function ReferenceSee is depracated as of mPDF 6. Please use IndexEntrySee instead.');
	}

	function CreateReference($a = 1, $b = '', $c = '', $d = 3, $e = 1, $f = '', $g = 5, $h = '', $i = '', $j = false)
	{
		$this->Error('function CreateReference is depracated as of mPDF 6. Please use InsertIndex instead.');
	}

	function CreateIndex($a = 1, $b = '', $c = '', $d = 3, $e = 1, $f = '', $g = 5, $h = '', $i = '', $j = false)
	{
		$this->Error('function CreateIndex is depracated as of mPDF 6. Please use InsertIndex instead.');
	}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function Close()
	{
		// Check old Aliases - now depracated mPDF 6
		if (isset($this->UnvalidatedText)) {
			$this->Error('$mpdf->UnvalidatedText is depracated as of mPDF 6. Please use $mpdf->watermarkText  instead.');
		}
		if (isset($this->TopicIsUnvalidated)) {
			$this->Error('$mpdf->TopicIsUnvalidated is depracated as of mPDF 6. Please use $mpdf->showWatermarkText instead.');
		}
		if (isset($this->AliasNbPg)) {
			$this->Error('$mpdf->AliasNbPg is depracated as of mPDF 6. Please use $mpdf->aliasNbPg instead.');
		}
		if (isset($this->AliasNbPgGp)) {
			$this->Error('$mpdf->AliasNbPgGp is depracated as of mPDF 6. Please use $mpdf->aliasNbPgGp instead.');
		}
		if (isset($this->BiDirectional)) {
			$this->Error('$mpdf->BiDirectional is depracated as of mPDF 6. Please use $mpdf->biDirectional instead.');
		}
		if (isset($this->Anchor2Bookmark)) {
			$this->Error('$mpdf->Anchor2Bookmark is depracated as of mPDF 6. Please use $mpdf->anchor2Bookmark instead.');
		}
		if (isset($this->KeepColumns)) {
			$this->Error('$mpdf->KeepColumns is depracated as of mPDF 6. Please use $mpdf->keepColumns instead.');
		}
		if (isset($this->useOddEven)) {
			$this->Error('$mpdf->useOddEven is depracated as of mPDF 6. Please use $mpdf->mirrorMargins instead.');
		}
		if (isset($this->useSubstitutionsMB)) {
			$this->Error('$mpdf->useSubstitutionsMB is depracated as of mPDF 6. Please use $mpdf->useSubstitutions instead.');
		}
		if (isset($this->useLang)) {
			$this->Error('$mpdf->useLang is depracated as of mPDF 6. Please use $mpdf->autoLangToFont instead.');
		}
		if (isset($this->useAutoFont)) {
			$this->Error('$mpdf->useAutoFont is depracated. Please use $mpdf->autoScriptToLang instead.');
		}

		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '2', 'Closing last page');
		} // *PROGRESS-BAR*
		//Terminate document
		if ($this->state == 3)
			return;
		if ($this->page == 0)
			$this->AddPage($this->CurOrientation);
		if (count($this->cellBorderBuffer)) {
			$this->printcellbuffer();
		} // *TABLES*
		if ($this->tablebuffer) {
			$this->printtablebuffer();
		} // *TABLES*
		/* -- COLUMNS -- */

		if ($this->ColActive) {
			$this->SetColumns(0);
			$this->ColActive = 0;
			if (count($this->columnbuffer)) {
				$this->printcolumnbuffer();
			}
		}
		/* -- END COLUMNS -- */

		// BODY Backgrounds
		$s = '';

		$s .= $this->PrintBodyBackgrounds();

		$s .= $this->PrintPageBackgrounds();
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', "\n" . $s . "\n" . '\\1', $this->pages[$this->page]);
		$this->pageBackgrounds = array();

		if ($this->visibility != 'visible')
			$this->SetVisibility('visible');
		$this->EndLayer();

		if (!$this->tocontents || !$this->tocontents->TOCmark) { //Page footer
			$this->InFooter = true;
			$this->Footer();
			$this->InFooter = false;
		}
		if ($this->tocontents && ($this->tocontents->TOCmark || count($this->tocontents->m_TOC))) {
			$this->tocontents->insertTOC();
		} // *TOC*
		//Close page
		$this->_endpage();

		//Close document
		$this->_enddoc();
	}

	

	function PrintBodyBackgrounds()
	{
		$s = '';
		$clx = 0;
		$cly = 0;
		$clw = $this->w;
		$clh = $this->h;
		// If using bleed and trim margins in paged media
		if ($this->pageDim[$this->page]['outer_width_LR'] || $this->pageDim[$this->page]['outer_width_TB']) {
			$clx = $this->pageDim[$this->page]['outer_width_LR'] - $this->pageDim[$this->page]['bleedMargin'];
			$cly = $this->pageDim[$this->page]['outer_width_TB'] - $this->pageDim[$this->page]['bleedMargin'];
			$clw = $this->w - 2 * $clx;
			$clh = $this->h - 2 * $cly;
		}

		if ($this->bodyBackgroundColor) {
			$s .= 'q ' . $this->SetFColor($this->bodyBackgroundColor, true) . "\n";
			if ($this->bodyBackgroundColor{0} == 5) { // RGBa
				$s .= $this->SetAlpha(ord($this->bodyBackgroundColor{4}) / 100, 'Normal', true, 'F') . "\n";
			} else if ($this->bodyBackgroundColor{0} == 6) { // CMYKa
				$s .= $this->SetAlpha(ord($this->bodyBackgroundColor{5}) / 100, 'Normal', true, 'F') . "\n";
			}
			$s .= sprintf('%.3F %.3F %.3F %.3F re f Q', ($clx * _MPDFK), ($cly * _MPDFK), $clw * _MPDFK, $clh * _MPDFK) . "\n";
		}

		/* -- BACKGROUNDS -- */
		if ($this->bodyBackgroundGradient) {
			$g = $this->grad->parseBackgroundGradient($this->bodyBackgroundGradient);
			if ($g) {
				$s .= $this->grad->Gradient($clx, $cly, $clw, $clh, (isset($g['gradtype']) ? $g['gradtype'] : null), $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true);
			}
		}
		if ($this->bodyBackgroundImage) {
			if (isset($this->bodyBackgroundImage['gradient']) && $this->bodyBackgroundImage['gradient'] && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $this->bodyBackgroundImage['gradient'])) {
				$g = $this->grad->parseMozGradient($this->bodyBackgroundImage['gradient']);
				if ($g) {
					$s .= $this->grad->Gradient($clx, $cly, $clw, $clh, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true);
				}
			} else if ($this->bodyBackgroundImage['image_id']) { // Background pattern
				$n = count($this->patterns) + 1;
				// If using resize, uses TrimBox (not including the bleed)
				list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($this->bodyBackgroundImage['orig_w'], $this->bodyBackgroundImage['orig_h'], $clw, $clh, $this->bodyBackgroundImage['resize'], $this->bodyBackgroundImage['x_repeat'], $this->bodyBackgroundImage['y_repeat']);

				$this->patterns[$n] = array('x' => $clx, 'y' => $cly, 'w' => $clw, 'h' => $clh, 'pgh' => $this->h, 'image_id' => $this->bodyBackgroundImage['image_id'], 'orig_w' => $orig_w, 'orig_h' => $orig_h, 'x_pos' => $this->bodyBackgroundImage['x_pos'], 'y_pos' => $this->bodyBackgroundImage['y_pos'], 'x_repeat' => $x_repeat, 'y_repeat' => $y_repeat, 'itype' => $this->bodyBackgroundImage['itype']);
				if (($this->bodyBackgroundImage['opacity'] > 0 || $this->bodyBackgroundImage['opacity'] === '0') && $this->bodyBackgroundImage['opacity'] < 1) {
					$opac = $this->SetAlpha($this->bodyBackgroundImage['opacity'], 'Normal', true);
				} else {
					$opac = '';
				}
				$s .= sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, ($clx * _MPDFK), ($cly * _MPDFK), $clw * _MPDFK, $clh * _MPDFK) . "\n";
			}
		}
		/* -- END BACKGROUNDS -- */
		return $s;
	}

	
	function PrintPageBackgrounds($adjustmenty = 0)
	{
		$s = '';

		ksort($this->pageBackgrounds);
		foreach ($this->pageBackgrounds AS $bl => $pbs) {
			foreach ($pbs AS $pb) {
				if ((!isset($pb['image_id']) && !isset($pb['gradient'])) || isset($pb['shadowonly'])) { // Background colour or boxshadow
					if ($pb['z-index'] > 0) {
						$this->current_layer = $pb['z-index'];
						$s .= "\n" . '/OCBZ-index /ZI' . $pb['z-index'] . ' BDC' . "\n";
					}

					if ($pb['visibility'] != 'visible') {
						if ($pb['visibility'] == 'printonly')
							$s .= '/OC /OC1 BDC' . "\n";
						else if ($pb['visibility'] == 'screenonly')
							$s .= '/OC /OC2 BDC' . "\n";
						else if ($pb['visibility'] == 'hidden')
							$s .= '/OC /OC3 BDC' . "\n";
					}
					// Box shadow
					if (isset($pb['shadow']) && $pb['shadow']) {
						$s .= $pb['shadow'] . "\n";
					}
					if (isset($pb['clippath']) && $pb['clippath']) {
						$s .= $pb['clippath'] . "\n";
					}
					$s .= 'q ' . $this->SetFColor($pb['col'], true) . "\n";
					if ($pb['col']{0} == 5) { // RGBa
						$s .= $this->SetAlpha(ord($pb['col']{4}) / 100, 'Normal', true, 'F') . "\n";
					} else if ($pb['col']{0} == 6) { // CMYKa
						$s .= $this->SetAlpha(ord($pb['col']{5}) / 100, 'Normal', true, 'F') . "\n";
					}
					$s .= sprintf('%.3F %.3F %.3F %.3F re f Q', $pb['x'] * _MPDFK, ($this->h - $pb['y']) * _MPDFK, $pb['w'] * _MPDFK, -$pb['h'] * _MPDFK) . "\n";
					if (isset($pb['clippath']) && $pb['clippath']) {
						$s .= 'Q' . "\n";
					}
					if ($pb['visibility'] != 'visible')
						$s .= 'EMC' . "\n";

					if ($pb['z-index'] > 0) {
						$s .= "\n" . 'EMCBZ-index' . "\n";
						$this->current_layer = 0;
					}
				}
			}
			/* -- BACKGROUNDS -- */
			foreach ($pbs AS $pb) {
				if ((isset($pb['gradient']) && $pb['gradient']) || (isset($pb['image_id']) && $pb['image_id'])) {
					if ($pb['z-index'] > 0) {
						$this->current_layer = $pb['z-index'];
						$s .= "\n" . '/OCGZ-index /ZI' . $pb['z-index'] . ' BDC' . "\n";
					}
					if ($pb['visibility'] != 'visible') {
						if ($pb['visibility'] == 'printonly')
							$s .= '/OC /OC1 BDC' . "\n";
						else if ($pb['visibility'] == 'screenonly')
							$s .= '/OC /OC2 BDC' . "\n";
						else if ($pb['visibility'] == 'hidden')
							$s .= '/OC /OC3 BDC' . "\n";
					}
				}
				if (isset($pb['gradient']) && $pb['gradient']) {
					if (isset($pb['clippath']) && $pb['clippath']) {
						$s .= $pb['clippath'] . "\n";
					}
					$s .= $this->grad->Gradient($pb['x'], $pb['y'], $pb['w'], $pb['h'], $pb['gradtype'], $pb['stops'], $pb['colorspace'], $pb['coords'], $pb['extend'], true);
					if (isset($pb['clippath']) && $pb['clippath']) {
						$s .= 'Q' . "\n";
					}
				} else if (isset($pb['image_id']) && $pb['image_id']) { // Background Image
					$pb['y'] -= $adjustmenty;
					$pb['h'] += $adjustmenty;
					$n = count($this->patterns) + 1;
					list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($pb['orig_w'], $pb['orig_h'], $pb['w'], $pb['h'], $pb['resize'], $pb['x_repeat'], $pb['y_repeat'], $pb['bpa'], $pb['size']);
					$this->patterns[$n] = array('x' => $pb['x'], 'y' => $pb['y'], 'w' => $pb['w'], 'h' => $pb['h'], 'pgh' => $this->h, 'image_id' => $pb['image_id'], 'orig_w' => $orig_w, 'orig_h' => $orig_h, 'x_pos' => $pb['x_pos'], 'y_pos' => $pb['y_pos'], 'x_repeat' => $x_repeat, 'y_repeat' => $y_repeat, 'itype' => $pb['itype'], 'bpa' => $pb['bpa']);
					$x = $pb['x'] * _MPDFK;
					$y = ($this->h - $pb['y']) * _MPDFK;
					$w = $pb['w'] * _MPDFK;
					$h = -$pb['h'] * _MPDFK;
					if (isset($pb['clippath']) && $pb['clippath']) {
						$s .= $pb['clippath'] . "\n";
					}
					if ($this->writingHTMLfooter || $this->writingHTMLheader) { // Write each (tiles) image rather than use as a pattern
						$iw = $pb['orig_w'] / _MPDFK;
						$ih = $pb['orig_h'] / _MPDFK;

						$w = $pb['w'];
						$h = $pb['h'];
						$x0 = $pb['x'];
						$y0 = $pb['y'];

						if (isset($pb['bpa']) && $pb['bpa']) {
							$w = $pb['bpa']['w'];
							$h = $pb['bpa']['h'];
							$x0 = $pb['bpa']['x'];
							$y0 = $pb['bpa']['y'];
						}

						if (isset($pb['size']['w']) && $pb['size']['w']) {
							$size = $pb['size'];

							if ($size['w'] == 'contain') {
								// Scale the image, while preserving its intrinsic aspect ratio (if any), to the largest size such that both its width and its height can fit inside the background positioning area.
								// Same as resize==3
								$ih = $ih * $pb['bpa']['w'] / $iw;
								$iw = $pb['bpa']['w'];
								if ($ih > $pb['bpa']['h']) {
									$iw = $iw * $pb['bpa']['h'] / $ih;
									$ih = $pb['bpa']['h'];
								}
							} else if ($size['w'] == 'cover') {
								// Scale the image, while preserving its intrinsic aspect ratio (if any), to the smallest size such that both its width and its height can completely cover the background positioning area.
								$ih = $ih * $pb['bpa']['w'] / $iw;
								$iw = $pb['bpa']['w'];
								if ($ih < $pb['bpa']['h']) {
									$iw = $iw * $ih / $pb['bpa']['h'];
									$ih = $pb['bpa']['h'];
								}
							} else {
								if (stristr($size['w'], '%')) {
									$size['w'] += 0;
									$size['w'] /= 100;
									$size['w'] = ($pb['bpa']['w'] * $size['w']);
								}
								if (stristr($size['h'], '%')) {
									$size['h'] += 0;
									$size['h'] /= 100;
									$size['h'] = ($pb['bpa']['h'] * $size['h']);
								}
								if ($size['w'] == 'auto' && $size['h'] == 'auto') {
									$iw = $iw;
									$ih = $ih;
								} else if ($size['w'] == 'auto' && $size['h'] != 'auto') {
									$iw = $iw * $size['h'] / $ih;
									$ih = $size['h'];
								} else if ($size['w'] != 'auto' && $size['h'] == 'auto') {
									$ih = $ih * $size['w'] / $iw;
									$iw = $size['w'];
								} else {
									$iw = $size['w'];
									$ih = $size['h'];
								}
							}
						}

						// Number to repeat
						if ($pb['x_repeat']) {
							$nx = ceil($pb['w'] / $iw) + 1;
						} else {
							$nx = 1;
						}
						if ($pb['y_repeat']) {
							$ny = ceil($pb['h'] / $ih) + 1;
						} else {
							$ny = 1;
						}

						$x_pos = $pb['x_pos'];
						if (stristr($x_pos, '%')) {
							$x_pos += 0;
							$x_pos /= 100;
							$x_pos = ($pb['bpa']['w'] * $x_pos) - ($iw * $x_pos);
						}
						$y_pos = $pb['y_pos'];
						if (stristr($y_pos, '%')) {
							$y_pos += 0;
							$y_pos /= 100;
							$y_pos = ($pb['bpa']['h'] * $y_pos) - ($ih * $y_pos);
						}
						if ($nx > 1) {
							while ($x_pos > ($pb['x'] - $pb['bpa']['x'])) {
								$x_pos -= $iw;
							}
						}
						if ($ny > 1) {
							while ($y_pos > ($pb['y'] - $pb['bpa']['y'])) {
								$y_pos -= $ih;
							}
						}
						for ($xi = 0; $xi < $nx; $xi++) {
							for ($yi = 0; $yi < $ny; $yi++) {
								$x = $x0 + $x_pos + ($iw * $xi);
								$y = $y0 + $y_pos + ($ih * $yi);
								if ($pb['opacity'] > 0 && $pb['opacity'] < 1) {
									$opac = $this->SetAlpha($pb['opacity'], 'Normal', true);
								} else {
									$opac = '';
								}
								$s .= sprintf("q %s %.3F 0 0 %.3F %.3F %.3F cm /I%d Do Q", $opac, $iw * _MPDFK, $ih * _MPDFK, $x * _MPDFK, ($this->h - ($y + $ih)) * _MPDFK, $pb['image_id']) . "\n";
							}
						}
					} else {
						if (($pb['opacity'] > 0 || $pb['opacity'] === '0') && $pb['opacity'] < 1) {
							$opac = $this->SetAlpha($pb['opacity'], 'Normal', true);
						} else {
							$opac = '';
						}
						$s .= sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, $x, $y, $w, $h) . "\n";
					}
					if (isset($pb['clippath']) && $pb['clippath']) {
						$s .= 'Q' . "\n";
					}
				}
				if ((isset($pb['gradient']) && $pb['gradient']) || (isset($pb['image_id']) && $pb['image_id'])) {
					if ($pb['visibility'] != 'visible')
						$s .= 'EMC' . "\n";

					if ($pb['z-index'] > 0) {
						$s .= "\n" . 'EMCGZ-index' . "\n";
						$this->current_layer = 0;
					}
				}
			}
			/* -- END BACKGROUNDS -- */
		}
		return $s;
	}

	

	

	function EndLayer()
	{
		if ($this->current_layer > 0) {
			$this->_out('EMCZ-index');
			$this->current_layer = 0;
		}
	}

	


	function AddPage($orientation = '', $condition = '', $resetpagenum = '', $pagenumstyle = '', $suppress = '', $mgl = '', $mgr = '', $mgt = '', $mgb = '', $mgh = '', $mgf = '', $ohname = '', $ehname = '', $ofname = '', $efname = '', $ohvalue = 0, $ehvalue = 0, $ofvalue = 0, $efvalue = 0, $pagesel = '', $newformat = '')
	{

		/* -- CSS-FLOAT -- */
		// Float DIV
		// Cannot do with columns on, or if any change in page orientation/margins etc.
		// If next page already exists - i.e background /headers and footers already written
		if ($this->state > 0 && $this->page < count($this->pages)) {
			$bak_cml = $this->cMarginL;
			$bak_cmr = $this->cMarginR;
			$bak_dw = $this->divwidth;
			// Paint Div Border if necessary
			if ($this->blklvl > 0) {
				$save_tr = $this->table_rotate; // *TABLES*
				$this->table_rotate = 0; // *TABLES*
				if ($this->y == $this->blk[$this->blklvl]['y0']) {
					$this->blk[$this->blklvl]['startpage'] ++;
				}
				if (($this->y > $this->blk[$this->blklvl]['y0']) || $this->flowingBlockAttr['is_table']) {
					$toplvl = $this->blklvl;
				} else {
					$toplvl = $this->blklvl - 1;
				}
				$sy = $this->y;
				for ($bl = 1; $bl <= $toplvl; $bl++) {
					$this->PaintDivBB('pagebottom', 0, $bl);
				}
				$this->y = $sy;
				$this->table_rotate = $save_tr; // *TABLES*
			}
			$s = $this->PrintPageBackgrounds();

			// Writes after the marker so not overwritten later by page background etc.
			$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', '\\1' . "\n" . $s . "\n", $this->pages[$this->page]);
			$this->pageBackgrounds = array();
			$family = $this->FontFamily;
			$style = $this->FontStyle;
			$size = $this->FontSizePt;
			$lw = $this->LineWidth;
			$dc = $this->DrawColor;
			$fc = $this->FillColor;
			$tc = $this->TextColor;
			$cf = $this->ColorFlag;

			$this->printfloatbuffer();

			//Move to next page
			$this->page++;
			$this->ResetMargins();
			$this->SetAutoPageBreak($this->autoPageBreak, $this->bMargin);
			$this->x = $this->lMargin;
			$this->y = $this->tMargin;
			$this->FontFamily = '';
			$this->_out('2 J');
			$this->LineWidth = $lw;
			$this->_out(sprintf('%.3F w', $lw * _MPDFK));
			if ($family)
				$this->SetFont($family, $style, $size, true, true);
			$this->DrawColor = $dc;
			if ($dc != $this->defDrawColor)
				$this->_out($dc);
			$this->FillColor = $fc;
			if ($fc != $this->defFillColor)
				$this->_out($fc);
			$this->TextColor = $tc;
			$this->ColorFlag = $cf;
			for ($bl = 1; $bl <= $this->blklvl; $bl++) {
				$this->blk[$bl]['y0'] = $this->y;
				// Don't correct more than once for background DIV containing a Float
				if (!isset($this->blk[$bl]['marginCorrected'][$this->page])) {
					$this->blk[$bl]['x0'] += $this->MarginCorrection;
				}
				$this->blk[$bl]['marginCorrected'][$this->page] = true;
			}
			$this->cMarginL = $bak_cml;
			$this->cMarginR = $bak_cmr;
			$this->divwidth = $bak_dw;
			return '';
		}
		/* -- END CSS-FLOAT -- */

		//Start a new page
		if ($this->state == 0)
			$this->Open();

		$bak_cml = $this->cMarginL;
		$bak_cmr = $this->cMarginR;
		$bak_dw = $this->divwidth;


		$bak_lh = $this->lineheight;

		$orientation = substr(strtoupper($orientation), 0, 1);
		$condition = strtoupper($condition);


		if ($condition == 'E') { // only adds new page if needed to create an Even page
			if (!$this->mirrorMargins || ($this->page) % 2 == 0) {
				return false;
			}
		} else if ($condition == 'O') { // only adds new page if needed to create an Odd page
			if (!$this->mirrorMargins || ($this->page) % 2 == 1) {
				return false;
			}
		} else if ($condition == 'NEXT-EVEN') { // always adds at least one new page to create an Even page
			if (!$this->mirrorMargins) {
				$condition = '';
			} else {
				if ($pagesel) {
					$pbch = $pagesel;
					$pagesel = '';
				} // *CSS-PAGE*
				else {
					$pbch = false;
				} // *CSS-PAGE*
				$this->AddPage($this->CurOrientation, 'O');
				$this->extrapagebreak = true; // mPDF 6 pagebreaktype
				if ($pbch) {
					$pagesel = $pbch;
				} // *CSS-PAGE*
				$condition = '';
			}
		} else if ($condition == 'NEXT-ODD') { // always adds at least one new page to create an Odd page
			if (!$this->mirrorMargins) {
				$condition = '';
			} else {
				if ($pagesel) {
					$pbch = $pagesel;
					$pagesel = '';
				} // *CSS-PAGE*
				else {
					$pbch = false;
				} // *CSS-PAGE*
				$this->AddPage($this->CurOrientation, 'E');
				$this->extrapagebreak = true; // mPDF 6 pagebreaktype
				if ($pbch) {
					$pagesel = $pbch;
				} // *CSS-PAGE*
				$condition = '';
			}
		}


		if ($resetpagenum || $pagenumstyle || $suppress) {
			$this->PageNumSubstitutions[] = array('from' => ($this->page + 1), 'reset' => $resetpagenum, 'type' => $pagenumstyle, 'suppress' => $suppress);
		}


		$save_tr = $this->table_rotate; // *TABLES*
		$this->table_rotate = 0; // *TABLES*
		$save_kwt = $this->kwt;
		$this->kwt = 0;
		$save_layer = $this->current_layer;
		$save_vis = $this->visibility;

		if ($this->visibility != 'visible')
			$this->SetVisibility('visible');
		$this->EndLayer();

		// Paint Div Border if necessary
		//PAINTS BACKGROUND COLOUR OR BORDERS for DIV - DISABLED FOR COLUMNS (cf. AcceptPageBreak) AT PRESENT in ->PaintDivBB
		if (!$this->ColActive && $this->blklvl > 0) {
			if (isset($this->blk[$this->blklvl]['y0']) && $this->y == $this->blk[$this->blklvl]['y0'] && !$this->extrapagebreak) { // mPDF 6 pagebreaktype
				if (isset($this->blk[$this->blklvl]['startpage'])) {
					$this->blk[$this->blklvl]['startpage'] ++;
				} else {
					$this->blk[$this->blklvl]['startpage'] = 1;
				}
			}
			if ((isset($this->blk[$this->blklvl]['y0']) && $this->y > $this->blk[$this->blklvl]['y0']) || $this->flowingBlockAttr['is_table'] || $this->extrapagebreak) {
				$toplvl = $this->blklvl;
			} // mPDF 6 pagebreaktype
			else {
				$toplvl = $this->blklvl - 1;
			}
			$sy = $this->y;
			for ($bl = 1; $bl <= $toplvl; $bl++) {

				if (isset($this->blk[$bl]['z-index']) && $this->blk[$bl]['z-index'] > 0) {
					$this->BeginLayer($this->blk[$bl]['z-index']);
				}
				if (isset($this->blk[$bl]['visibility']) && $this->blk[$bl]['visibility'] && $this->blk[$bl]['visibility'] != 'visible') {
					$this->SetVisibility($this->blk[$bl]['visibility']);
				}
				$this->PaintDivBB('pagebottom', 0, $bl);
			}
			$this->y = $sy;
			// RESET block y0 and x0 - see below
		}
		$this->extrapagebreak = false; // mPDF 6 pagebreaktype

		if ($this->visibility != 'visible')
			$this->SetVisibility('visible');
		$this->EndLayer();

		// BODY Backgrounds
		if ($this->page > 0) {
			$s = '';
			$s .= $this->PrintBodyBackgrounds();

			$s .= $this->PrintPageBackgrounds();
			$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', "\n" . $s . "\n" . '\\1', $this->pages[$this->page]);
			$this->pageBackgrounds = array();
		}

		$save_kt = $this->keep_block_together;
		$this->keep_block_together = 0;

		$save_cols = false;
		/* -- COLUMNS -- */
		if ($this->ColActive) {
			$save_cols = true;
			$save_nbcol = $this->NbCol; // other values of gap and vAlign will not change by setting Columns off
			$this->SetColumns(0);
		}
		/* -- END COLUMNS -- */


		$family = $this->FontFamily;
		$style = $this->FontStyle;
		$size = $this->FontSizePt;
		$this->ColumnAdjust = true; // enables column height adjustment for the page
		$lw = $this->LineWidth;
		$dc = $this->DrawColor;
		$fc = $this->FillColor;
		$tc = $this->TextColor;
		$cf = $this->ColorFlag;
		if ($this->page > 0) {
			//Page footer
			$this->InFooter = true;

			$this->Reset();
			$this->pageoutput[$this->page] = array();

			$this->Footer();
			//Close page
			$this->_endpage();
		}


		//Start new page
		$this->_beginpage($orientation, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
		if ($this->docTemplate) {
			$pagecount = $this->SetSourceFile($this->docTemplate);
			if (($this->page - $this->docTemplateStart) > $pagecount) {
				if ($this->docTemplateContinue) {
					$tplIdx = $this->ImportPage($pagecount);
					$this->UseTemplate($tplIdx);
				}
			} else {
				$tplIdx = $this->ImportPage(($this->page - $this->docTemplateStart));
				$this->UseTemplate($tplIdx);
			}
		}
		if ($this->pageTemplate) {
			$this->UseTemplate($this->pageTemplate);
		}

		// Tiling Patterns
		$this->_out('___PAGE___START' . $this->uniqstr);
		$this->_out('___BACKGROUND___PATTERNS' . $this->uniqstr);
		$this->_out('___HEADER___MARKER' . $this->uniqstr);
		$this->pageBackgrounds = array();

		//Set line cap style to square
		$this->SetLineCap(2);
		//Set line width
		$this->LineWidth = $lw;
		$this->_out(sprintf('%.3F w', $lw * _MPDFK));
		//Set font
		if ($family)
			$this->SetFont($family, $style, $size, true, true); // forces write

//Set colors
		$this->DrawColor = $dc;
		if ($dc != $this->defDrawColor)
			$this->_out($dc);
		$this->FillColor = $fc;
		if ($fc != $this->defFillColor)
			$this->_out($fc);
		$this->TextColor = $tc;
		$this->ColorFlag = $cf;

		//Page header
		$this->Header();

		//Restore line width
		if ($this->LineWidth != $lw) {
			$this->LineWidth = $lw;
			$this->_out(sprintf('%.3F w', $lw * _MPDFK));
		}
		//Restore font
		if ($family)
			$this->SetFont($family, $style, $size, true, true); // forces write

//Restore colors
		if ($this->DrawColor != $dc) {
			$this->DrawColor = $dc;
			$this->_out($dc);
		}
		if ($this->FillColor != $fc) {
			$this->FillColor = $fc;
			$this->_out($fc);
		}
		$this->TextColor = $tc;
		$this->ColorFlag = $cf;
		$this->InFooter = false;

		if ($save_layer > 0)
			$this->BeginLayer($save_layer);

		if ($save_vis != 'visible')
			$this->SetVisibility($save_vis);

		/* -- COLUMNS -- */
		if ($save_cols) {
			// Restore columns
			$this->SetColumns($save_nbcol, $this->colvAlign, $this->ColGap);
		}
		if ($this->ColActive) {
			$this->SetCol(0);
		}
		/* -- END COLUMNS -- */


		//RESET BLOCK BORDER TOP
		if (!$this->ColActive) {
			for ($bl = 1; $bl <= $this->blklvl; $bl++) {
				$this->blk[$bl]['y0'] = $this->y;
				if (isset($this->blk[$bl]['x0'])) {
					$this->blk[$bl]['x0'] += $this->MarginCorrection;
				} else {
					$this->blk[$bl]['x0'] = $this->MarginCorrection;
				}
				// Added mPDF 3.0 Float DIV
				$this->blk[$bl]['marginCorrected'][$this->page] = true;
			}
		}


		$this->table_rotate = $save_tr; // *TABLES*
		$this->kwt = $save_kwt;

		$this->keep_block_together = $save_kt;

		$this->cMarginL = $bak_cml;
		$this->cMarginR = $bak_cmr;
		$this->divwidth = $bak_dw;

		$this->lineheight = $bak_lh;
	}

	function PageNo()
	{
		//Get current page number
		return $this->page;
	}

	

	function SetColor($col, $type = '')
	{
		$out = '';
		if (!$col) {
			return '';
		} // mPDF 6
		if ($col{0} == 3 || $col{0} == 5) { // RGB / RGBa
			$out = sprintf('%.3F %.3F %.3F rg', ord($col{1}) / 255, ord($col{2}) / 255, ord($col{3}) / 255);
		} else if ($col{0} == 1) { // GRAYSCALE
			$out = sprintf('%.3F g', ord($col{1}) / 255);
		} else if ($col{0} == 2) { // SPOT COLOR
			$out = sprintf('/CS%d cs %.3F scn', ord($col{1}), ord($col{2}) / 100);
		} else if ($col{0} == 4 || $col{0} == 6) { // CMYK / CMYKa
			$out = sprintf('%.3F %.3F %.3F %.3F k', ord($col{1}) / 100, ord($col{2}) / 100, ord($col{3}) / 100, ord($col{4}) / 100);
		}
		if ($type == 'Draw') {
			$out = strtoupper($out);
		} // e.g. rg => RG
		else if ($type == 'CodeOnly') {
			$out = preg_replace('/\s(rg|g|k)/', '', $out);
		}
		return $out;
	}

	function SetDColor($col, $return = false)
	{
		$out = $this->SetColor($col, 'Draw');
		if ($return) {
			return $out;
		}
		if ($out == '') {
			return '';
		}
		$this->DrawColor = $out;
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['DrawColor']) && $this->pageoutput[$this->page]['DrawColor'] != $this->DrawColor) || !isset($this->pageoutput[$this->page]['DrawColor']))) {
			$this->_out($this->DrawColor);
		}
		$this->pageoutput[$this->page]['DrawColor'] = $this->DrawColor;
	}

	function SetFColor($col, $return = false)
	{
		$out = $this->SetColor($col, 'Fill');
		if ($return) {
			return $out;
		}
		if ($out == '') {
			return '';
		}
		$this->FillColor = $out;
		$this->ColorFlag = ($out != $this->TextColor);
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['FillColor']) && $this->pageoutput[$this->page]['FillColor'] != $this->FillColor) || !isset($this->pageoutput[$this->page]['FillColor']))) {
			$this->_out($this->FillColor);
		}
		$this->pageoutput[$this->page]['FillColor'] = $this->FillColor;
	}

	function SetTColor($col, $return = false)
	{
		$out = $this->SetColor($col, 'Text');
		if ($return) {
			return $out;
		}
		if ($out == '') {
			return '';
		}
		$this->TextColor = $out;
		$this->ColorFlag = ($this->FillColor != $out);
	}

	
	function GetCharWidthCore($c)
	{
		//Get width of a single character in the current Core font
		$c = (string) $c;
		$w = 0;
		// Soft Hyphens chr(173)
		if ($c == chr(173) && $this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats') {
			return 0;
		} else if (($this->textvar & FC_SMALLCAPS) && isset($this->upperCase[ord($c)])) {  // mPDF 5.7.1
			$charw = $this->CurrentFont['cw'][chr($this->upperCase[ord($c)])];
			if ($charw !== false) {
				$charw = $charw * $this->smCapsScale * $this->smCapsStretch / 100;
				$w+=$charw;
			}
		} else if (isset($this->CurrentFont['cw'][$c])) {
			$w += $this->CurrentFont['cw'][$c];
		} else if (isset($this->CurrentFont['cw'][ord($c)])) {
			$w += $this->CurrentFont['cw'][ord($c)];
		}
		$w *= ($this->FontSize / 1000);
		if ($this->minwSpacing || $this->fixedlSpacing) {
			if ($c == ' ')
				$nb_spaces = 1;
			else
				$nb_spaces = 0;
			$w += $this->fixedlSpacing + ($nb_spaces * $this->minwSpacing);
		}
		return ($w);
	}

	

	function GetCharWidth($c, $addSubset = true)
	{
		if (!$this->usingCoreFont) {
			return $this->GetCharWidthNonCore($c, $addSubset);
		} else {
			return $this->GetCharWidthCore($c);
		}
	}

	function GetStringWidth($s, $addSubset = true, $OTLdata = false, $textvar = 0, $includeKashida = false)
	{ // mPDF 5.7.1
		//Get width of a string in the current font
		$s = (string) $s;
		$cw = &$this->CurrentFont['cw'];
		$w = 0;
		$kerning = 0;
		$lastchar = 0;
		$nb_carac = 0;
		$nb_spaces = 0;
		$kashida = 0;
		// mPDF ITERATION
		if ($this->iterationCounter)
			$s = preg_replace('/{iteration ([a-zA-Z0-9_]+)}/', '\\1', $s);
		if (!$this->usingCoreFont) {
			$discards = substr_count($s, "\xc2\xad"); // mPDF 6 soft hyphens [U+00AD]
			$unicode = $this->UTF8StringToArray($s, $addSubset);
			if ($this->minwSpacing || $this->fixedlSpacing) {
				$nb_spaces = mb_substr_count($s, ' ', $this->mb_enc);
				$nb_carac = count($unicode) - $discards; // mPDF 6
				// mPDF 5.7.1
				// Use GPOS OTL
				if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
					if (isset($OTLdata['group']) && $OTLdata['group']) {
						$nb_carac -= substr_count($OTLdata['group'], 'M');
					}
				}
			}
			/* -- CJK-FONTS -- */
			if ($this->CurrentFont['type'] == 'Type0') { // CJK Adobe fonts
				foreach ($unicode as $char) {
					if ($char == 0x00AD) {
						continue;
					} // mPDF 6 soft hyphens [U+00AD]
					if (isset($cw[$char])) {
						$w+=$cw[$char];
					} elseif (isset($this->CurrentFont['MissingWidth'])) {
						$w += $this->CurrentFont['MissingWidth'];
					} else {
						$w += 500;
					}
				}
			} else {
				/* -- END CJK-FONTS -- */
				foreach ($unicode as $i => $char) {
					if ($char == 0x00AD) {
						continue;
					} // mPDF 6 soft hyphens [U+00AD]
					if (($textvar & FC_SMALLCAPS) && isset($this->upperCase[$char])) {
						$charw = $this->_getCharWidth($cw, $this->upperCase[$char]);
						if ($charw !== false) {
							$charw = $charw * $this->smCapsScale * $this->smCapsStretch / 100;
							$w+=$charw;
						} elseif (isset($this->CurrentFont['desc']['MissingWidth'])) {
							$w += $this->CurrentFont['desc']['MissingWidth'];
						} elseif (isset($this->CurrentFont['MissingWidth'])) {
							$w += $this->CurrentFont['MissingWidth'];
						} else {
							$w += 500;
						}
					} else {
						$charw = $this->_getCharWidth($cw, $char);
						if ($charw !== false) {
							$w+=$charw;
						} elseif (isset($this->CurrentFont['desc']['MissingWidth'])) {
							$w += $this->CurrentFont['desc']['MissingWidth'];
						} elseif (isset($this->CurrentFont['MissingWidth'])) {
							$w += $this->CurrentFont['MissingWidth'];
						} else {
							$w += 500;
						}
						// mPDF 5.7.1
						// Use GPOS OTL
						// ...GetStringWidth...
						if (isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0xFF) && !empty($OTLdata)) {
							if (isset($OTLdata['GPOSinfo'][$i]['wDir']) && $OTLdata['GPOSinfo'][$i]['wDir'] == 'RTL') {
								if (isset($OTLdata['GPOSinfo'][$i]['XAdvanceR']) && $OTLdata['GPOSinfo'][$i]['XAdvanceR']) {
									$w += $OTLdata['GPOSinfo'][$i]['XAdvanceR'] * 1000 / $this->CurrentFont['unitsPerEm'];
								}
							} else {
								if (isset($OTLdata['GPOSinfo'][$i]['XAdvanceL']) && $OTLdata['GPOSinfo'][$i]['XAdvanceL']) {
									$w += $OTLdata['GPOSinfo'][$i]['XAdvanceL'] * 1000 / $this->CurrentFont['unitsPerEm'];
								}
							}
							// Kashida from GPOS
							// Kashida is set as an absolute length value (already set as a proportion based on useKashida %)
							if ($includeKashida && isset($OTLdata['GPOSinfo'][$i]['kashida_space']) && $OTLdata['GPOSinfo'][$i]['kashida_space']) {
								$kashida += $OTLdata['GPOSinfo'][$i]['kashida_space'];
							}
						}
						if (($textvar & FC_KERNING) && $lastchar) {
							if (isset($this->CurrentFont['kerninfo'][$lastchar][$char])) {
								$kerning += $this->CurrentFont['kerninfo'][$lastchar][$char];
							}
						}
						$lastchar = $char;
					}
				}
			} // *CJK-FONTS*
		} else {
			if ($this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats') {
				$s = str_replace(chr(173), '', $s);
			}
			$nb_carac = $l = strlen($s);
			if ($this->minwSpacing || $this->fixedlSpacing) {
				$nb_spaces = substr_count($s, ' ');
			}
			for ($i = 0; $i < $l; $i++) {
				if (($textvar & FC_SMALLCAPS) && isset($this->upperCase[ord($s[$i])])) {  // mPDF 5.7.1
					$charw = $cw[chr($this->upperCase[ord($s[$i])])];
					if ($charw !== false) {
						$charw = $charw * $this->smCapsScale * $this->smCapsStretch / 100;
						$w+=$charw;
					}
				} else if (isset($cw[$s[$i]])) {
					$w += $cw[$s[$i]];
				} else if (isset($cw[ord($s[$i])])) {
					$w += $cw[ord($s[$i])];
				}
				if (($textvar & FC_KERNING) && $i > 0) { // mPDF 5.7.1
					if (isset($this->CurrentFont['kerninfo'][$s[($i - 1)]][$s[$i]])) {
						$kerning += $this->CurrentFont['kerninfo'][$s[($i - 1)]][$s[$i]];
					}
				}
			}
		}
		unset($cw);
		if ($textvar & FC_KERNING) {
			$w += $kerning;
		} // mPDF 5.7.1
		$w *= ($this->FontSize / 1000);
		$w += (($nb_carac + $nb_spaces) * $this->fixedlSpacing) + ($nb_spaces * $this->minwSpacing);
		$w += $kashida / _MPDFK;

		return ($w);
	}

	function SetLineWidth($width)
	{
		//Set line width
		$this->LineWidth = $width;
		$lwout = (sprintf('%.3F w', $width * _MPDFK));
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['LineWidth']) && $this->pageoutput[$this->page]['LineWidth'] != $lwout) || !isset($this->pageoutput[$this->page]['LineWidth']))) {
			$this->_out($lwout);
		}
		$this->pageoutput[$this->page]['LineWidth'] = $lwout;
	}

	


	function SetFont($family, $style = '', $size = 0, $write = true, $forcewrite = false)
	{
		$family = strtolower($family);
		if (!$this->onlyCoreFonts) {
			if ($family == 'sans' || $family == 'sans-serif') {
				$family = $this->sans_fonts[0];
			}
			if ($family == 'serif') {
				$family = $this->serif_fonts[0];
			}
			if ($family == 'mono' || $family == 'monospace') {
				$family = $this->mono_fonts[0];
			}
		}
		if (isset($this->fonttrans[$family]) && $this->fonttrans[$family]) {
			$family = $this->fonttrans[$family];
		}
		if ($family == '') {
			if ($this->FontFamily) {
				$family = $this->FontFamily;
			} else if ($this->default_font) {
				$family = $this->default_font;
			} else {
				$this->Error("No font or default font set!");
			}
		}
		$this->ReqFontStyle = $style; // required or requested style - used later for artificial bold/italic

		if (($family == 'csymbol') || ($family == 'czapfdingbats') || ($family == 'ctimes') || ($family == 'ccourier') || ($family == 'chelvetica')) {
			if ($this->PDFA || $this->PDFX) {
				if ($family == 'csymbol' || $family == 'czapfdingbats') {
					$this->Error("Symbol and Zapfdingbats cannot be embedded in mPDF (required for PDFA1-b or PDFX/1-a).");
				}
				if ($family == 'ctimes' || $family == 'ccourier' || $family == 'chelvetica') {
					if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) {
						$this->PDFAXwarnings[] = "Core Adobe font " . ucfirst($family) . " cannot be embedded in mPDF, which is required for PDFA1-b or PDFX/1-a. (Embedded font will be substituted.)";
					}
					if ($family == 'chelvetica') {
						$family = 'sans';
					}
					if ($family == 'ctimes') {
						$family = 'serif';
					}
					if ($family == 'ccourier') {
						$family = 'mono';
					}
				}
				$this->usingCoreFont = false;
			} else {
				$this->usingCoreFont = true;
			}
			if ($family == 'csymbol' || $family == 'czapfdingbats') {
				$style = '';
			}
		} else {
			$this->usingCoreFont = false;
		}

		// mPDF 5.7.1
		if ($style) {
			$style = strtoupper($style);
			if ($style == 'IB')
				$style = 'BI';
		}
		if ($size == 0)
			$size = $this->FontSizePt;

		$fontkey = $family . $style;

		$stylekey = $style;
		if (!$stylekey) {
			$stylekey = "R";
		}

		if (!$this->onlyCoreFonts && !$this->usingCoreFont) {
			if (!isset($this->fonts[$fontkey]) || count($this->default_available_fonts) != count($this->available_unifonts)) { // not already added
				/* -- CJK-FONTS -- */
				// CJK fonts
				if (in_array($fontkey, $this->available_CJK_fonts)) {
					if (!isset($this->fonts[$fontkey])) { // already added
						if (empty($this->Big5_widths)) {
							require(_MPDF_PATH . 'includes/CJKdata.php');
						}
						$this->AddCJKFont($family); // don't need to add style
					}
				}
				// Test to see if requested font/style is available - or substitute
				else
				/* -- END CJK-FONTS -- */
				if (!in_array($fontkey, $this->available_unifonts)) {
					// If font[nostyle] exists - set it
					if (in_array($family, $this->available_unifonts)) {
						$style = '';
					}

					// Else if only one font available - set it (assumes if only one font available it will not have a style)
					else if (count($this->available_unifonts) == 1) {
						$family = $this->available_unifonts[0];
						$style = '';
					} else {
						$found = 0;
						// else substitute font of similar type
						if (in_array($family, $this->sans_fonts)) {
							$i = array_intersect($this->sans_fonts, $this->available_unifonts);
							if (count($i)) {
								$i = array_values($i);
								// with requested style if possible
								if (!in_array(($i[0] . $style), $this->available_unifonts)) {
									$style = '';
								}
								$family = $i[0];
								$found = 1;
							}
						} else if (in_array($family, $this->serif_fonts)) {
							$i = array_intersect($this->serif_fonts, $this->available_unifonts);
							if (count($i)) {
								$i = array_values($i);
								// with requested style if possible
								if (!in_array(($i[0] . $style), $this->available_unifonts)) {
									$style = '';
								}
								$family = $i[0];
								$found = 1;
							}
						} else if (in_array($family, $this->mono_fonts)) {
							$i = array_intersect($this->mono_fonts, $this->available_unifonts);
							if (count($i)) {
								$i = array_values($i);
								// with requested style if possible
								if (!in_array(($i[0] . $style), $this->available_unifonts)) {
									$style = '';
								}
								$family = $i[0];
								$found = 1;
							}
						}

						if (!$found) {
							// set first available font
							$fs = $this->available_unifonts[0];
							preg_match('/^([a-z_0-9\-]+)([BI]{0,2})$/', $fs, $fas); // Allow "-"
							// with requested style if possible
							$ws = $fas[1] . $style;
							if (in_array($ws, $this->available_unifonts)) {
								$family = $fas[1]; // leave $style as is
							} else if (in_array($fas[1], $this->available_unifonts)) {
								// or without style
								$family = $fas[1];
								$style = '';
							} else {
								// or with the style specified
								$family = $fas[1];
								$style = $fas[2];
							}
						}
					}
					$fontkey = $family . $style;
				}
			}
			// try to add font (if not already added)
			$this->AddFont($family, $style);

			//Test if font is already selected
			if ($this->FontFamily == $family && $this->FontFamily == $this->currentfontfamily && $this->FontStyle == $style && $this->FontStyle == $this->currentfontstyle && $this->FontSizePt == $size && $this->FontSizePt == $this->currentfontsize && !$forcewrite) {
				return $family;
			}

			$fontkey = $family . $style;

			//Select it
			$this->FontFamily = $family;
			$this->FontStyle = $style;
			$this->FontSizePt = $size;
			$this->FontSize = $size / _MPDFK;
			$this->CurrentFont = &$this->fonts[$fontkey];
			if ($write) {
				$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
				if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']))) {
					$this->_out($fontout);
				}
				$this->pageoutput[$this->page]['Font'] = $fontout;
			}



			// Added - currentfont (lowercase) used in HTML2PDF
			$this->currentfontfamily = $family;
			$this->currentfontsize = $size;
			$this->currentfontstyle = $style;
			$this->setMBencoding('UTF-8');
		} else {  // if using core fonts
			if ($this->PDFA || $this->PDFX) {
				$this->Error('Core Adobe fonts cannot be embedded in mPDF (required for PDFA1-b or PDFX/1-a) - cannot use option to use core fonts.');
			}
			$this->setMBencoding('windows-1252');

			//Test if font is already selected
			if (($this->FontFamily == $family) AND ( $this->FontStyle == $style) AND ( $this->FontSizePt == $size) && !$forcewrite) {
				return $family;
			}

			if (!isset($this->CoreFonts[$fontkey])) {
				if (in_array($family, $this->serif_fonts)) {
					$family = 'ctimes';
				} else if (in_array($family, $this->mono_fonts)) {
					$family = 'ccourier';
				} else {
					$family = 'chelvetica';
				}
				$this->usingCoreFont = true;
				$fontkey = $family . $style;
			}

			if (!isset($this->fonts[$fontkey])) {
				// STANDARD CORE FONTS
				if (isset($this->CoreFonts[$fontkey])) {
					//Load metric file
					$file = $family;
					if ($family == 'ctimes' || $family == 'chelvetica' || $family == 'ccourier') {
						$file.=strtolower($style);
					}
					$file.='.php';
					include(_MPDF_PATH . 'font/' . $file);
					if (!isset($cw)) {
						$this->Error('Could not include font metric file');
					}
					$i = count($this->fonts) + $this->extraFontSubsets + 1;
					$this->fonts[$fontkey] = array('i' => $i, 'type' => 'core', 'name' => $this->CoreFonts[$fontkey], 'desc' => $desc, 'up' => $up, 'ut' => $ut, 'cw' => $cw);
					if ($this->useKerning && isset($kerninfo)) {
						$this->fonts[$fontkey]['kerninfo'] = $kerninfo;
					}
				} else {
					$this->Error('mPDF error - Font not defined');
				}
			}
			//Test if font is already selected
			if (($this->FontFamily == $family) AND ( $this->FontStyle == $style) AND ( $this->FontSizePt == $size) && !$forcewrite) {
				return $family;
			}
			//Select it
			$this->FontFamily = $family;
			$this->FontStyle = $style;
			$this->FontSizePt = $size;
			$this->FontSize = $size / _MPDFK;
			$this->CurrentFont = &$this->fonts[$fontkey];
			if ($write) {
				$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
				if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']))) {
					$this->_out($fontout);
				}
				$this->pageoutput[$this->page]['Font'] = $fontout;
			}
			// Added - currentfont (lowercase) used in HTML2PDF
			$this->currentfontfamily = $family;
			$this->currentfontsize = $size;
			$this->currentfontstyle = $style;
		}

		return $family;
	}

	function SetFontSize($size, $write = true)
	{
		//Set font size in points
		if ($this->FontSizePt == $size)
			return;
		$this->FontSizePt = $size;
		$this->FontSize = $size / _MPDFK;
		$this->currentfontsize = $size;
		if ($write) {
			$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			// Edited mPDF 3.0
			if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']))) {
				$this->_out($fontout);
			}
			$this->pageoutput[$this->page]['Font'] = $fontout;
		}
	}

	function Link($x, $y, $w, $h, $link)
	{
		$l = array($x * _MPDFK, $this->hPt - $y * _MPDFK, $w * _MPDFK, $h * _MPDFK, $link);
		if ($this->keep_block_together) { // don't write yet
			return;
		} else if ($this->table_rotate) { // *TABLES*
			$this->tbrot_Links[$this->page][] = $l; // *TABLES*
			return; // *TABLES*
		} // *TABLES*
		else if ($this->kwt) {
			$this->kwt_Links[$this->page][] = $l;
			return;
		}

		if ($this->writingHTMLheader || $this->writingHTMLfooter) {
			$this->HTMLheaderPageLinks[] = $l;
			return;
		}
		//Put a link on the page
		$this->PageLinks[$this->page][] = $l;
		// Save cross-reference to Column buffer
		$ref = count($this->PageLinks[$this->page]) - 1; // *COLUMNS*
		$this->columnLinks[$this->CurrCol][INTVAL($this->x)][INTVAL($this->y)] = $ref; // *COLUMNS*
	}

	


	/* -- END DIRECTW -- */

	function ResetSpacing()
	{
		if ($this->ws != 0) {
			$this->_out('BT 0 Tw ET');
		}
		$this->ws = 0;
		if ($this->charspacing != 0) {
			$this->_out('BT 0 Tc ET');
		}
		$this->charspacing = 0;
	}

	function SetSpacing($cs, $ws)
	{
		if (intval($cs * 1000) == 0) {
			$cs = 0;
		}
		if ($cs) {
			$this->_out(sprintf('BT %.3F Tc ET', $cs));
		} else if ($this->charspacing != 0) {
			$this->_out('BT 0 Tc ET');
		}
		$this->charspacing = $cs;
		if (intval($ws * 1000) == 0) {
			$ws = 0;
		}
		if ($ws) {
			$this->_out(sprintf('BT %.3F Tw ET', $ws));
		} else if ($this->ws != 0) {
			$this->_out('BT 0 Tw ET');
		}
		$this->ws = $ws;
	}


	function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '', $currentx = 0, $lcpaddingL = 0, $lcpaddingR = 0, $valign = 'M', $spanfill = 0, $exactWidth = false, $OTLdata = false, $textvar = 0, $lineBox = false)
	{  // mPDF 5.7.1
		//Output a cell
		// Expects input to be mb_encoded if necessary and RTL reversed
		// NON_BREAKING SPACE
		if ($this->usingCoreFont) {
			$txt = str_replace(chr(160), chr(32), $txt);
		} else {
			$txt = str_replace(chr(194) . chr(160), chr(32), $txt);
		}

		$oldcolumn = $this->CurrCol;
		// Automatic page break
		// Allows PAGE-BREAK-AFTER = avoid to work
		if (isset($this->blk[$this->blklvl])) {
			$bottom = $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['margin_bottom'];
		} else {
			$bottom = 0;
		}
		if (!$this->tableLevel && (($this->y + $this->divheight > $this->PageBreakTrigger) || ($this->y + $h > $this->PageBreakTrigger) ||
			($this->y + ($h * 2) + $bottom > $this->PageBreakTrigger && $this->blk[$this->blklvl]['page_break_after_avoid'])) and ! $this->InFooter and $this->AcceptPageBreak()) { // mPDF 5.7.2
			$x = $this->x; //Current X position
			// WORD SPACING
			$ws = $this->ws; //Word Spacing
			$charspacing = $this->charspacing; //Character Spacing
			$this->ResetSpacing();

			$this->AddPage($this->CurOrientation);
			// Added to correct for OddEven Margins
			$x += $this->MarginCorrection;
			if ($currentx) {
				$currentx += $this->MarginCorrection;
			}
			$this->x = $x;
			// WORD SPACING
			$this->SetSpacing($charspacing, $ws);
		}

		// Test: to put line through centre of cell: $this->Line($this->x,$this->y+($h/2),$this->x+50,$this->y+($h/2));
		// Test: to put border around cell as it is specified: $border='LRTB';


		/* -- COLUMNS -- */
		// COLS
		// COLUMN CHANGE
		if ($this->CurrCol != $oldcolumn) {
			if ($currentx) {
				$currentx += $this->ChangeColumn * ($this->ColWidth + $this->ColGap);
			}
			$this->x += $this->ChangeColumn * ($this->ColWidth + $this->ColGap);
		}

		// COLUMNS Update/overwrite the lowest bottom of printing y value for a column
		if ($this->ColActive) {
			if ($h) {
				$this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y + $h;
			} else {
				$this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y + $this->divheight;
			}
		}
		/* -- END COLUMNS -- */


		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$s = '';
		if ($fill == 1 && $this->FillColor) {
			if ((isset($this->pageoutput[$this->page]['FillColor']) && $this->pageoutput[$this->page]['FillColor'] != $this->FillColor) || !isset($this->pageoutput[$this->page]['FillColor'])) {
				$s .= $this->FillColor . ' ';
			}
			$this->pageoutput[$this->page]['FillColor'] = $this->FillColor;
		}


		if ($lineBox && isset($lineBox['boxtop']) && $txt) { // i.e. always from WriteFlowingBlock/finishFlowingBlock (but not objects -
			// which only have $lineBox['top'] set)
			$boxtop = $this->y + $lineBox['boxtop'];
			$boxbottom = $this->y + $lineBox['boxbottom'];
			$glyphYorigin = $lineBox['glyphYorigin'];
			$baseline_shift = $lineBox['baseline-shift'];
			$bord_boxtop = $bg_boxtop = $boxtop = $boxtop - $baseline_shift;
			$bord_boxbottom = $bg_boxbottom = $boxbottom = $boxbottom - $baseline_shift;
			$bord_boxheight = $bg_boxheight = $boxheight = $boxbottom - $boxtop;

			// If inline element BACKGROUND has bounding box set by parent element:
			if (isset($lineBox['background-boxtop'])) {
				$bg_boxtop = $this->y + $lineBox['background-boxtop'] - $lineBox['background-baseline-shift'];
				$bg_boxbottom = $this->y + $lineBox['background-boxbottom'] - $lineBox['background-baseline-shift'];
				$bg_boxheight = $bg_boxbottom - $bg_boxtop;
			}
			// If inline element BORDER has bounding box set by parent element:
			if (isset($lineBox['border-boxtop'])) {
				$bord_boxtop = $this->y + $lineBox['border-boxtop'] - $lineBox['border-baseline-shift'];
				$bord_boxbottom = $this->y + $lineBox['border-boxbottom'] - $lineBox['border-baseline-shift'];
				$bord_boxheight = $bord_boxbottom - $bord_boxtop;
			}
		} else {
			$boxtop = $this->y;
			$boxheight = $h;
			$boxbottom = $this->y + $h;
			$baseline_shift = 0;
			if ($txt != '') {
				// FONT SIZE - this determines the baseline caculation
				$bfs = $this->FontSize;
				//Calculate baseline Superscript and Subscript Y coordinate adjustment
				$bfx = $this->baselineC;
				$baseline = $bfx * $bfs;

				if ($textvar & FA_SUPERSCRIPT) {
					$baseline_shift = $this->textparam['text-baseline'];
				} // mPDF 5.7.1	// mPDF 6
				else if ($textvar & FA_SUBSCRIPT) {
					$baseline_shift = $this->textparam['text-baseline'];
				} // mPDF 5.7.1	// mPDF 6
				else if ($this->bullet) {
					$baseline += ($bfx - 0.7) * $this->FontSize;
				}

				// Vertical align (for Images)
				if ($valign == 'T') {
					$va = (0.5 * $bfs * $this->normalLineheight);
				} else if ($valign == 'B') {
					$va = $h - (0.5 * $bfs * $this->normalLineheight);
				} else {
					$va = 0.5 * $h;
				} // Middle
				// ONLY SET THESE IF WANT TO CONFINE BORDER +/- FILL TO FIT FONTSIZE - NOT FULL CELL AS IS ORIGINAL FUNCTION
				// spanfill or spanborder are set in FlowingBlock functions
				if ($spanfill || !empty($this->spanborddet) || $link != '') {
					$exth = 0.2; // Add to fontsize to increase height of background / link / border
					$boxtop = $this->y + $baseline + $va - ($this->FontSize * (1 + $exth / 2) * (0.5 + $bfx));
					$boxheight = $this->FontSize * (1 + $exth);
					$boxbottom = $boxtop + $boxheight;
				}
				$glyphYorigin = $baseline + $va;
			}
			$boxtop -= $baseline_shift;
			$boxbottom -= $baseline_shift;
			$bord_boxtop = $bg_boxtop = $boxtop;
			$bord_boxbottom = $bg_boxbottom = $boxbottom;
			$bord_boxheight = $bg_boxheight = $boxheight = $boxbottom - $boxtop;
		}


		$bbw = $tbw = $lbw = $rbw = 0; // Border widths
		if (!empty($this->spanborddet)) {
			if (!isset($this->spanborddet['B'])) {
				$this->spanborddet['B'] = array('s' => 0, 'style' => '', 'w' => 0);
			}
			if (!isset($this->spanborddet['T'])) {
				$this->spanborddet['T'] = array('s' => 0, 'style' => '', 'w' => 0);
			}
			if (!isset($this->spanborddet['L'])) {
				$this->spanborddet['L'] = array('s' => 0, 'style' => '', 'w' => 0);
			}
			if (!isset($this->spanborddet['R'])) {
				$this->spanborddet['R'] = array('s' => 0, 'style' => '', 'w' => 0);
			}
			$bbw = $this->spanborddet['B']['w'];
			$tbw = $this->spanborddet['T']['w'];
			$lbw = $this->spanborddet['L']['w'];
			$rbw = $this->spanborddet['R']['w'];
		}
		if ($fill == 1 || $border == 1 || !empty($this->spanborddet)) {
			if (!empty($this->spanborddet)) {
				if ($fill == 1) {
					$s.=sprintf('%.3F %.3F %.3F %.3F re f ', ($this->x - $lbw) * _MPDFK, ($this->h - $bg_boxtop + $tbw) * _MPDFK, ($w + $lbw + $rbw) * _MPDFK, (-$bg_boxheight - $tbw - $bbw) * _MPDFK);
				}
				$s.= ' q ';
				$dashon = 3;
				$dashoff = 3.5;
				$dot = 2.5;
				if ($tbw) {
					$short = 0;
					if ($this->spanborddet['T']['style'] == 'dashed') {
						$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ', $tbw * $dashon * _MPDFK, $tbw * $dashoff * _MPDFK);
					} else if ($this->spanborddet['T']['style'] == 'dotted') {
						$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ', 0.001, $tbw * $dot * _MPDFK, -$tbw / 2 * _MPDFK);
						$short = $tbw / 2;
					} else {
						$s.=' 0 j 0 J [] 0 d ';
					}
					if ($this->spanborddet['T']['style'] != 'dotted') {
						$s .= 'q ';
						$s .= sprintf('%.3F %.3F m ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w + $rbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w) * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x) * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK);
						$s .= ' h W n '; // Ends path no-op & Sets the clipping path
					}
					$c = $this->SetDColor($this->spanborddet['T']['c'], true);
					if ($this->spanborddet['T']['style'] == 'double') {
						$s.=sprintf(' %s %.3F w ', $c, $tbw / 3 * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw * 5 / 6) * _MPDFK, ($this->x + $w + $rbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw * 5 / 6) * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw / 6) * _MPDFK, ($this->x + $w + $rbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw / 6) * _MPDFK);
					} else if ($this->spanborddet['T']['style'] == 'dotted') {
						$s.=sprintf(' %s %.3F w ', $c, $tbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw / 2) * _MPDFK, ($this->x + $w + $rbw - $short) * _MPDFK, ($this->h - $bord_boxtop + $tbw / 2) * _MPDFK);
					} else {
						$s.=sprintf(' %s %.3F w ', $c, $tbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw / 2) * _MPDFK, ($this->x + $w + $rbw - $short) * _MPDFK, ($this->h - $bord_boxtop + $tbw / 2) * _MPDFK);
					}
					if ($this->spanborddet['T']['style'] != 'dotted') {
						$s .= ' Q ';
					}
				}
				if ($bbw) {
					$short = 0;
					if ($this->spanborddet['B']['style'] == 'dashed') {
						$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ', $bbw * $dashon * _MPDFK, $bbw * $dashoff * _MPDFK);
					} else if ($this->spanborddet['B']['style'] == 'dotted') {
						$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ', 0.001, $bbw * $dot * _MPDFK, -$bbw / 2 * _MPDFK);
						$short = $bbw / 2;
					} else {
						$s.=' 0 j 0 J [] 0 d ';
					}
					if ($this->spanborddet['B']['style'] != 'dotted') {
						$s .= 'q ';
						$s .= sprintf('%.3F %.3F m ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w + $rbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w) * _MPDFK, ($this->h - $bord_boxbottom) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x) * _MPDFK, ($this->h - $bord_boxbottom) * _MPDFK);
						$s .= ' h W n '; // Ends path no-op & Sets the clipping path
					}
					$c = $this->SetDColor($this->spanborddet['B']['c'], true);
					if ($this->spanborddet['B']['style'] == 'double') {
						$s.=sprintf(' %s %.3F w ', $c, $bbw / 3 * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw / 6) * _MPDFK, ($this->x + $w + $rbw - $short) * _MPDFK, ($this->h - $bord_boxbottom - $bbw / 6) * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw * 5 / 6) * _MPDFK, ($this->x + $w + $rbw - $short) * _MPDFK, ($this->h - $bord_boxbottom - $bbw * 5 / 6) * _MPDFK);
					} else if ($this->spanborddet['B']['style'] == 'dotted') {
						$s.=sprintf(' %s %.3F w ', $c, $bbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw / 2) * _MPDFK, ($this->x + $w + $rbw - $short) * _MPDFK, ($this->h - $bord_boxbottom - $bbw / 2) * _MPDFK);
					} else {
						$s.=sprintf(' %s %.3F w ', $c, $bbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw / 2) * _MPDFK, ($this->x + $w + $rbw - $short) * _MPDFK, ($this->h - $bord_boxbottom - $bbw / 2) * _MPDFK);
					}
					if ($this->spanborddet['B']['style'] != 'dotted') {
						$s .= ' Q ';
					}
				}
				if ($lbw) {
					$short = 0;
					if ($this->spanborddet['L']['style'] == 'dashed') {
						$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ', $lbw * $dashon * _MPDFK, $lbw * $dashoff * _MPDFK);
					} else if ($this->spanborddet['L']['style'] == 'dotted') {
						$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ', 0.001, $lbw * $dot * _MPDFK, -$lbw / 2 * _MPDFK);
						$short = $lbw / 2;
					} else {
						$s.=' 0 j 0 J [] 0 d ';
					}
					if ($this->spanborddet['L']['style'] != 'dotted') {
						$s .= 'q ';
						$s .= sprintf('%.3F %.3F m ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x) * _MPDFK, ($this->h - $bord_boxbottom) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x) * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x - $lbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK);
						$s .= ' h W n '; // Ends path no-op & Sets the clipping path
					}
					$c = $this->SetDColor($this->spanborddet['L']['c'], true);
					if ($this->spanborddet['L']['style'] == 'double') {
						$s.=sprintf(' %s %.3F w ', $c, $lbw / 3 * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw / 6) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x - $lbw / 6) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw * 5 / 6) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x - $lbw * 5 / 6) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
					} else if ($this->spanborddet['L']['style'] == 'dotted') {
						$s.=sprintf(' %s %.3F w ', $c, $lbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw / 2) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x - $lbw / 2) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
					} else {
						$s.=sprintf(' %s %.3F w ', $c, $lbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x - $lbw / 2) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x - $lbw / 2) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
					}
					if ($this->spanborddet['L']['style'] != 'dotted') {
						$s .= ' Q ';
					}
				}
				if ($rbw) {
					$short = 0;
					if ($this->spanborddet['R']['style'] == 'dashed') {
						$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ', $rbw * $dashon * _MPDFK, $rbw * $dashoff * _MPDFK);
					} else if ($this->spanborddet['R']['style'] == 'dotted') {
						$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ', 0.001, $rbw * $dot * _MPDFK, -$rbw / 2 * _MPDFK);
						$short = $rbw / 2;
					} else {
						$s.=' 0 j 0 J [] 0 d ';
					}
					if ($this->spanborddet['R']['style'] != 'dotted') {
						$s .= 'q ';
						$s .= sprintf('%.3F %.3F m ', ($this->x + $w + $rbw) * _MPDFK, ($this->h - $bord_boxbottom - $bbw) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w) * _MPDFK, ($this->h - $bord_boxbottom) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w) * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK);
						$s .= sprintf('%.3F %.3F l ', ($this->x + $w + $rbw) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK);
						$s .= ' h W n '; // Ends path no-op & Sets the clipping path
					}
					$c = $this->SetDColor($this->spanborddet['R']['c'], true);
					if ($this->spanborddet['R']['style'] == 'double') {
						$s.=sprintf(' %s %.3F w ', $c, $rbw / 3 * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x + $w + $rbw / 6) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x + $w + $rbw / 6) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x + $w + $rbw * 5 / 6) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x + $w + $rbw * 5 / 6) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
					} else if ($this->spanborddet['R']['style'] == 'dotted') {
						$s.=sprintf(' %s %.3F w ', $c, $rbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x + $w + $rbw / 2) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x + $w + $rbw / 2) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
					} else {
						$s.=sprintf(' %s %.3F w ', $c, $rbw * _MPDFK);
						$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($this->x + $w + $rbw / 2) * _MPDFK, ($this->h - $bord_boxtop + $tbw) * _MPDFK, ($this->x + $w + $rbw / 2) * _MPDFK, ($this->h - $bord_boxbottom - $bbw + $short) * _MPDFK);
					}
					if ($this->spanborddet['R']['style'] != 'dotted') {
						$s .= ' Q ';
					}
				}
				$s.= ' Q ';
			} else { // If "border", does not come from WriteFlowingBlock or FinishFlowingBlock
				if ($fill == 1)
					$op = ($border == 1) ? 'B' : 'f';
				else
					$op = 'S';
				$s.=sprintf('%.3F %.3F %.3F %.3F re %s ', $this->x * _MPDFK, ($this->h - $bg_boxtop) * _MPDFK, $w * _MPDFK, -$bg_boxheight * _MPDFK, $op);
			}
		}

		if (is_string($border)) { // If "border", does not come from WriteFlowingBlock or FinishFlowingBlock
			$x = $this->x;
			$y = $this->y;
			if (is_int(strpos($border, 'L')))
				$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', $x * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK, $x * _MPDFK, ($this->h - ($bord_boxbottom)) * _MPDFK);
			if (is_int(strpos($border, 'T')))
				$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', $x * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK, ($x + $w) * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK);
			if (is_int(strpos($border, 'R')))
				$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', ($x + $w) * _MPDFK, ($this->h - $bord_boxtop) * _MPDFK, ($x + $w) * _MPDFK, ($this->h - ($bord_boxbottom)) * _MPDFK);
			if (is_int(strpos($border, 'B')))
				$s.=sprintf('%.3F %.3F m %.3F %.3F l S ', $x * _MPDFK, ($this->h - ($bord_boxbottom)) * _MPDFK, ($x + $w) * _MPDFK, ($this->h - ($bord_boxbottom)) * _MPDFK);
		}

		if ($txt != '') {


			if ($exactWidth)
				$stringWidth = $w;
			else
				$stringWidth = $this->GetStringWidth($txt, true, $OTLdata, $textvar) + ( $this->charspacing * mb_strlen($txt, $this->mb_enc) / _MPDFK ) + ( $this->ws * mb_substr_count($txt, ' ', $this->mb_enc) / _MPDFK );

			// Set x OFFSET FOR PRINTING
			if ($align == 'R') {
				$dx = $w - $this->cMarginR - $stringWidth - $lcpaddingR;
			} elseif ($align == 'C') {
				$dx = (($w - $stringWidth ) / 2);
			} elseif ($align == 'L' or $align == 'J')
				$dx = $this->cMarginL + $lcpaddingL;
			else
				$dx = 0;

			if ($this->ColorFlag)
				$s .='q ' . $this->TextColor . ' ';

			// OUTLINE
			if (isset($this->textparam['outline-s']) && $this->textparam['outline-s'] && !($textvar & FC_SMALLCAPS)) { // mPDF 5.7.1
				$s .=' ' . sprintf('%.3F w', $this->LineWidth * _MPDFK) . ' ';
				$s .=" $this->DrawColor ";
				$s .=" 2 Tr ";
			} else if ($this->falseBoldWeight && strpos($this->ReqFontStyle, "B") !== false && strpos($this->FontStyle, "B") === false && !($textvar & FC_SMALLCAPS)) { // can't use together with OUTLINE or Small Caps	// mPDF 5.7.1	??? why not with SmallCaps ???
				$s .= ' 2 Tr 1 J 1 j ';
				$s .= ' ' . sprintf('%.3F w', ($this->FontSize / 130) * _MPDFK * $this->falseBoldWeight) . ' ';
				$tc = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
				if ($this->FillColor != $tc) {
					$s .= ' ' . $tc . ' ';
				}  // stroke (outline) = same colour as text(fill)
			} else {
				$s .=" 0 Tr ";
			}

			if (strpos($this->ReqFontStyle, "I") !== false && strpos($this->FontStyle, "I") === false) { // Artificial italic
				$aix = '1 0 0.261799 1 %.3F %.3F Tm ';
			} else {
				$aix = '%.3F %.3F Td ';
			}

			$px = ($this->x + $dx) * _MPDFK;
			$py = ($this->h - ($this->y + $glyphYorigin - $baseline_shift)) * _MPDFK;

			// THE TEXT
			$txt2 = $txt;
			$sub = '';
			$this->CurrentFont['used'] = true;

			/*             * ************** SIMILAR TO Text() ************************ */

			// IF corefonts AND NOT SmCaps AND NOT Kerning
			// Just output text; charspacing and wordspacing already set by charspacing (Tc) and ws (Tw)
			if ($this->usingCoreFont && !($textvar & FC_SMALLCAPS) && !($textvar & FC_KERNING)) {
				$txt2 = $this->_escape($txt2);
				$sub .=sprintf('BT ' . $aix . ' (%s) Tj ET', $px, $py, $txt2);
			}


			// IF NOT corefonts AND NO wordspacing AND NOT SIP/SMP AND NOT SmCaps AND NOT Kerning AND NOT OTL
			// Just output text
			else if (!$this->usingCoreFont && !$this->ws && !($textvar & FC_SMALLCAPS) && !($textvar & FC_KERNING) && !(isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0xFF) && !empty($OTLdata['GPOSinfo']))) {
				// IF SIP/SMP
				if ((isset($this->CurrentFont['sip']) && $this->CurrentFont['sip']) || (isset($this->CurrentFont['smp']) && $this->CurrentFont['smp'])) {
					$txt2 = $this->UTF8toSubset($txt2);
					$sub .=sprintf('BT ' . $aix . ' %s Tj ET', $px, $py, $txt2);
				}
				// NOT SIP/SMP
				else {
					$txt2 = $this->UTF8ToUTF16BE($txt2, false);
					$txt2 = $this->_escape($txt2);
					$sub .=sprintf('BT ' . $aix . ' (%s) Tj ET', $px, $py, $txt2);
				}
			}


			// IF NOT corefonts AND IS wordspacing AND NOT SIP AND NOT SmCaps AND NOT Kerning AND NOT OTL
			// Output text word by word with an adjustment to the intercharacter spacing for SPACEs to form word spacing
			// IF multibyte - Tw has no effect - need to do word spacing using an adjustment before each space
			else if (!$this->usingCoreFont && $this->ws && !((isset($this->CurrentFont['sip']) && $this->CurrentFont['sip']) || (isset($this->CurrentFont['smp']) && $this->CurrentFont['smp'])) && !($textvar & FC_SMALLCAPS) && !($textvar & FC_KERNING) && !(isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0xFF) && (!empty($OTLdata['GPOSinfo']) || (strpos($OTLdata['group'], 'M') !== false && $this->charspacing)) )) {
				$space = " ";
				$space = $this->UTF8ToUTF16BE($space, false);
				$space = $this->_escape($space);
				$sub .=sprintf('BT ' . $aix . ' %.3F Tc [', $px, $py, $this->charspacing);
				$t = explode(' ', $txt2);
				$numt = count($t);
				for ($i = 0; $i < $numt; $i++) {
					$tx = $t[$i];
					$tx = $this->UTF8ToUTF16BE($tx, false);
					$tx = $this->_escape($tx);
					$sub .=sprintf('(%s) ', $tx);
					if (($i + 1) < $numt) {
						$adj = -($this->ws) * 1000 / $this->FontSizePt;
						$sub .=sprintf('%d(%s) ', $adj, $space);
					}
				}
				$sub .='] TJ ';
				$sub .=' ET';
			}


			// ELSE (IF SmCaps || Kerning || OTL) [corefonts or not corefonts; SIP or SMP or BMP]
			else {
				$sub = $this->applyGPOSpdf($txt, $aix, $px, $py, $OTLdata, $textvar);
			}

			/*             * ************** END SIMILAR TO Text() ************************ */

			if ($this->shrin_k > 1) {
				$shrin_k = $this->shrin_k;
			} else {
				$shrin_k = 1;
			}
			// UNDERLINE
			if ($textvar & FD_UNDERLINE) { // mPDF 5.7.1	// mPDF 6
				// mPDF 5.7.3  inline text-decoration parameters
				$c = $this->textparam['u-decoration']['color'];
				if ($this->FillColor != $c) {
					$sub .= ' ' . $c . ' ';
				}
				// mPDF 5.7.3  inline text-decoration parameters
				$decorationfontkey = $this->textparam['u-decoration']['fontkey'];
				$decorationfontsize = $this->textparam['u-decoration']['fontsize'] / $shrin_k;
				if (isset($this->fonts[$decorationfontkey]['ut']) && $this->fonts[$decorationfontkey]['ut']) {
					$ut = $this->fonts[$decorationfontkey]['ut'] / 1000 * $decorationfontsize;
				} else {
					$ut = 60 / 1000 * $decorationfontsize;
				}
				if (isset($this->fonts[$decorationfontkey]['up']) && $this->fonts[$decorationfontkey]['up']) {
					$up = $this->fonts[$decorationfontkey]['up'];
				} else {
					$up = -100;
				}
				$adjusty = (-$up / 1000 * $decorationfontsize) + $ut / 2;
				$ubaseline = $glyphYorigin - $this->textparam['u-decoration']['baseline'] / $shrin_k;
				$olw = $this->LineWidth;
				$sub .=' ' . (sprintf(' %.3F w 0 j 0 J ', $ut * _MPDFK));
				$sub .=' ' . $this->_dounderline($this->x + $dx, $this->y + $ubaseline + $adjusty, $txt, $OTLdata, $textvar);
				$sub .=' ' . (sprintf(' %.3F w 2 j 2 J ', $olw * _MPDFK));
				if ($this->FillColor != $c) {
					$sub .= ' ' . $this->FillColor . ' ';
				}
			}

			// STRIKETHROUGH
			if ($textvar & FD_LINETHROUGH) { // mPDF 5.7.1	// mPDF 6
				// mPDF 5.7.3  inline text-decoration parameters
				$c = $this->textparam['s-decoration']['color'];
				if ($this->FillColor != $c) {
					$sub .= ' ' . $c . ' ';
				}
				// mPDF 5.7.3  inline text-decoration parameters
				$decorationfontkey = $this->textparam['s-decoration']['fontkey'];
				$decorationfontsize = $this->textparam['s-decoration']['fontsize'] / $shrin_k;
				// Use yStrikeoutSize from OS/2 if available
				if (isset($this->fonts[$decorationfontkey]['strs']) && $this->fonts[$decorationfontkey]['strs']) {
					$ut = $this->fonts[$decorationfontkey]['strs'] / 1000 * $decorationfontsize;
				}
				// else use underlineThickness from post if available
				else if (isset($this->fonts[$decorationfontkey]['ut']) && $this->fonts[$decorationfontkey]['ut']) {
					$ut = $this->fonts[$decorationfontkey]['ut'] / 1000 * $decorationfontsize;
				} else {
					$ut = 50 / 1000 * $decorationfontsize;
				}
				// Use yStrikeoutPosition from OS/2 if available
				if (isset($this->fonts[$decorationfontkey]['strp']) && $this->fonts[$decorationfontkey]['strp']) {
					$up = $this->fonts[$decorationfontkey]['strp'];
					$adjusty = (-$up / 1000 * $decorationfontsize);
				}
				// else use a fraction ($this->baselineS) of CapHeight
				else {
					if (isset($this->fonts[$decorationfontkey]['desc']['CapHeight']) && $this->fonts[$decorationfontkey]['desc']['CapHeight']) {
						$ch = $this->fonts[$decorationfontkey]['desc']['CapHeight'];
					} else {
						$ch = 700;
					}
					$adjusty = (-$ch / 1000 * $decorationfontsize) * $this->baselineS;
				}

				$sbaseline = $glyphYorigin - $this->textparam['s-decoration']['baseline'] / $shrin_k;
				$olw = $this->LineWidth;
				$sub .=' ' . (sprintf(' %.3F w 0 j 0 J ', $ut * _MPDFK));
				$sub .=' ' . $this->_dounderline($this->x + $dx, $this->y + $sbaseline + $adjusty, $txt, $OTLdata, $textvar);
				$sub .=' ' . (sprintf(' %.3F w 2 j 2 J ', $olw * _MPDFK));
				if ($this->FillColor != $c) {
					$sub .= ' ' . $this->FillColor . ' ';
				}
			}

			// mPDF 5.7.3  inline text-decoration parameters
			// OVERLINE
			if ($textvar & FD_OVERLINE) { // mPDF 5.7.1	// mPDF 6
				// mPDF 5.7.3  inline text-decoration parameters
				$c = $this->textparam['o-decoration']['color'];
				if ($this->FillColor != $c) {
					$sub .= ' ' . $c . ' ';
				}
				// mPDF 5.7.3  inline text-decoration parameters
				$decorationfontkey = $this->textparam['o-decoration']['fontkey'] / $shrin_k;
				$decorationfontsize = $this->textparam['o-decoration']['fontsize'];
				if (isset($this->fonts[$decorationfontkey]['ut']) && $this->fonts[$decorationfontkey]['ut']) {
					$ut = $this->fonts[$decorationfontkey]['ut'] / 1000 * $decorationfontsize;
				} else {
					$ut = 60 / 1000 * $decorationfontsize;
				}
				if (isset($this->fonts[$decorationfontkey]['desc']['CapHeight']) && $this->fonts[$decorationfontkey]['desc']['CapHeight']) {
					$ch = $this->fonts[$decorationfontkey]['desc']['CapHeight'];
				} else {
					$ch = 700;
				}
				$adjusty = (-$ch / 1000 * $decorationfontsize) * $this->baselineO;
				$obaseline = $glyphYorigin - $this->textparam['o-decoration']['baseline'] / $shrin_k;
				$olw = $this->LineWidth;
				$sub .=' ' . (sprintf(' %.3F w 0 j 0 J ', $ut * _MPDFK));
				$sub .=' ' . $this->_dounderline($this->x + $dx, $this->y + $obaseline + $adjusty, $txt, $OTLdata, $textvar);
				$sub .=' ' . (sprintf(' %.3F w 2 j 2 J ', $olw * _MPDFK));
				if ($this->FillColor != $c) {
					$sub .= ' ' . $this->FillColor . ' ';
				}
			}

			// TEXT SHADOW
			if ($this->textshadow) {  // First to process is last in CSS comma separated shadows
				foreach ($this->textshadow AS $ts) {
					$s .= ' q ';
					$s .= $this->SetTColor($ts['col'], true) . "\n";
					if ($ts['col']{0} == 5 && ord($ts['col']{4}) < 100) { // RGBa
						$s .= $this->SetAlpha(ord($ts['col']{4}) / 100, 'Normal', true, 'F') . "\n";
					} else if ($ts['col']{0} == 6 && ord($ts['col']{5}) < 100) { // CMYKa
						$s .= $this->SetAlpha(ord($ts['col']{5}) / 100, 'Normal', true, 'F') . "\n";
					} else if ($ts['col']{0} == 1 && $ts['col']{2} == 1 && ord($ts['col']{3}) < 100) { // Gray
						$s .= $this->SetAlpha(ord($ts['col']{3}) / 100, 'Normal', true, 'F') . "\n";
					}
					$s .= sprintf(' 1 0 0 1 %.4F %.4F cm', $ts['x'] * _MPDFK, -$ts['y'] * _MPDFK) . "\n";
					$s .= $sub;
					$s .= ' Q ';
				}
			}

			$s .= $sub;

			// COLOR
			if ($this->ColorFlag)
				$s .=' Q';

			// LINK
			if ($link != '') {
				$this->Link($this->x, $boxtop, $w, $boxheight, $link);
			}
		}
		if ($s)
			$this->_out($s);

		// WORD SPACING
		if ($this->ws && !$this->usingCoreFont) {
			$this->_out(sprintf('BT %.3F Tc ET', $this->charspacing));
		}
		$this->lasth = $h;
		if (strpos($txt, "\n") !== false)
			$ln = 1; // cell recognizes \n from <BR> tag
		if ($ln > 0) {
			//Go to next line
			$this->y += $h;
			if ($ln == 1) {
				//Move to next line
				if ($currentx != 0) {
					$this->x = $currentx;
				} else {
					$this->x = $this->lMargin;
				}
			}
		} else
			$this->x+=$w;
	}



	/* -- DIRECTW -- */



	/* -- END DIRECTW -- */


	/* -- HTML-CSS -- */

	function saveInlineProperties()
	{
		$saved = array();
		$saved['family'] = $this->FontFamily;
		$saved['style'] = $this->FontStyle;
		$saved['sizePt'] = $this->FontSizePt;
		$saved['size'] = $this->FontSize;
		$saved['HREF'] = $this->HREF;
		$saved['textvar'] = $this->textvar; // mPDF 5.7.1
		$saved['OTLtags'] = $this->OTLtags; // mPDF 5.7.1
		$saved['textshadow'] = $this->textshadow;
		$saved['linewidth'] = $this->LineWidth;
		$saved['drawcolor'] = $this->DrawColor;
		$saved['textparam'] = $this->textparam;
		$saved['lSpacingCSS'] = $this->lSpacingCSS;
		$saved['wSpacingCSS'] = $this->wSpacingCSS;
		$saved['I'] = $this->I;
		$saved['B'] = $this->B;
		$saved['colorarray'] = $this->colorarray;
		$saved['bgcolorarray'] = $this->spanbgcolorarray;
		$saved['border'] = $this->spanborddet;
		$saved['color'] = $this->TextColor;
		$saved['bgcolor'] = $this->FillColor;
		$saved['lang'] = $this->currentLang;
		$saved['fontLanguageOverride'] = $this->fontLanguageOverride; // mPDF 5.7.1
		$saved['display_off'] = $this->inlineDisplayOff;

		return $saved;
	}

	function restoreInlineProperties(&$saved)
	{
		$FontFamily = $saved['family'];
		$this->FontStyle = $saved['style'];
		$this->FontSizePt = $saved['sizePt'];
		$this->FontSize = $saved['size'];

		$this->currentLang = $saved['lang'];
		$this->fontLanguageOverride = $saved['fontLanguageOverride']; // mPDF 5.7.1

		$this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well

		$this->HREF = $saved['HREF'];
		$this->textvar = $saved['textvar']; // mPDF 5.7.1
		$this->OTLtags = $saved['OTLtags']; // mPDF 5.7.1
		$this->textshadow = $saved['textshadow'];
		$this->LineWidth = $saved['linewidth'];
		$this->DrawColor = $saved['drawcolor'];
		$this->textparam = $saved['textparam'];
		$this->inlineDisplayOff = $saved['display_off'];

		$this->lSpacingCSS = $saved['lSpacingCSS'];
		if (($this->lSpacingCSS || $this->lSpacingCSS === '0') && strtoupper($this->lSpacingCSS) != 'NORMAL') {
			$this->fixedlSpacing = $this->ConvertSize($this->lSpacingCSS, $this->FontSize);
		} else {
			$this->fixedlSpacing = false;
		}
		$this->wSpacingCSS = $saved['wSpacingCSS'];
		if ($this->wSpacingCSS && strtoupper($this->wSpacingCSS) != 'NORMAL') {
			$this->minwSpacing = $this->ConvertSize($this->wSpacingCSS, $this->FontSize);
		} else {
			$this->minwSpacing = 0;
		}

		$this->SetFont($FontFamily, $saved['style'], $saved['sizePt'], false);

		$this->currentfontstyle = $saved['style'];
		$this->currentfontsize = $saved['sizePt'];
		$this->SetStylesArray(array('B' => $saved['B'], 'I' => $saved['I'])); // mPDF 5.7.1

		$this->TextColor = $saved['color'];
		$this->FillColor = $saved['bgcolor'];
		$this->colorarray = $saved['colorarray'];
		$cor = $saved['colorarray'];
		if ($cor)
			$this->SetTColor($cor);
		$this->spanbgcolorarray = $saved['bgcolorarray'];
		$cor = $saved['bgcolorarray'];
		if ($cor)
			$this->SetFColor($cor);
		$this->spanborddet = $saved['border'];
	}
//-------------------------FLOWING BLOCK------------------------------------//
//The following functions were originally written by Damon Kohler           //
//--------------------------------------------------------------------------//

	function saveFont()
	{
		$saved = array();
		$saved['family'] = $this->FontFamily;
		$saved['style'] = $this->FontStyle;
		$saved['sizePt'] = $this->FontSizePt;
		$saved['size'] = $this->FontSize;
		$saved['curr'] = &$this->CurrentFont;
		$saved['lang'] = $this->currentLang; // mPDF 6
		$saved['color'] = $this->TextColor;
		$saved['spanbgcolor'] = $this->spanbgcolor;
		$saved['spanbgcolorarray'] = $this->spanbgcolorarray;
		$saved['bord'] = $this->spanborder;
		$saved['border'] = $this->spanborddet;
		$saved['HREF'] = $this->HREF;
		$saved['textvar'] = $this->textvar; // mPDF 5.7.1
		$saved['textshadow'] = $this->textshadow;
		$saved['linewidth'] = $this->LineWidth;
		$saved['drawcolor'] = $this->DrawColor;
		$saved['textparam'] = $this->textparam;
		$saved['ReqFontStyle'] = $this->ReqFontStyle;
		$saved['fixedlSpacing'] = $this->fixedlSpacing;
		$saved['minwSpacing'] = $this->minwSpacing;
		return $saved;
	}

	function restoreFont(&$saved, $write = true)
	{
		if (!isset($saved) || empty($saved))
			return;

		$this->FontFamily = $saved['family'];
		$this->FontStyle = $saved['style'];
		$this->FontSizePt = $saved['sizePt'];
		$this->FontSize = $saved['size'];
		$this->CurrentFont = &$saved['curr'];
		$this->currentLang = $saved['lang']; // mPDF 6
		$this->TextColor = $saved['color'];
		$this->spanbgcolor = $saved['spanbgcolor'];
		$this->spanbgcolorarray = $saved['spanbgcolorarray'];
		$this->spanborder = $saved['bord'];
		$this->spanborddet = $saved['border'];
		$this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well
		$this->HREF = $saved['HREF'];
		$this->fixedlSpacing = $saved['fixedlSpacing'];
		$this->minwSpacing = $saved['minwSpacing'];
		$this->textvar = $saved['textvar'];  // mPDF 5.7.1
		$this->textshadow = $saved['textshadow'];
		$this->LineWidth = $saved['linewidth'];
		$this->DrawColor = $saved['drawcolor'];
		$this->textparam = $saved['textparam'];
		if ($write) {
			$this->SetFont($saved['family'], $saved['style'], $saved['sizePt'], true, true); // force output
			$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']))) {
				$this->_out($fontout);
			}
			$this->pageoutput[$this->page]['Font'] = $fontout;
		} else
			$this->SetFont($saved['family'], $saved['style'], $saved['sizePt'], false);
		$this->ReqFontStyle = $saved['ReqFontStyle'];
	}

	function newFlowingBlock($w, $h, $a = '', $is_table = false, $blockstate = 0, $newblock = true, $blockdir = 'ltr', $table_draft = false)
	{
		if (!$a) {
			if ($blockdir == 'rtl') {
				$a = 'R';
			} else {
				$a = 'L';
			}
		}
		$this->flowingBlockAttr['width'] = ($w * _MPDFK);
		// line height in user units
		$this->flowingBlockAttr['is_table'] = $is_table;
		$this->flowingBlockAttr['table_draft'] = $table_draft;
		$this->flowingBlockAttr['height'] = $h;
		$this->flowingBlockAttr['lineCount'] = 0;
		$this->flowingBlockAttr['align'] = $a;
		$this->flowingBlockAttr['font'] = array();
		$this->flowingBlockAttr['content'] = array();
		$this->flowingBlockAttr['contentB'] = array();
		$this->flowingBlockAttr['contentWidth'] = 0;
		$this->flowingBlockAttr['blockstate'] = $blockstate;

		$this->flowingBlockAttr['newblock'] = $newblock;
		$this->flowingBlockAttr['valign'] = 'M';
		$this->flowingBlockAttr['blockdir'] = $blockdir;
		$this->flowingBlockAttr['cOTLdata'] = array(); // mPDF 5.7.1
		$this->flowingBlockAttr['lastBidiText'] = ''; // mPDF 5.7.1
		if (!empty($this->otl)) {
			$this->otl->lastBidiStrongType = '';
		} // *OTL*
	}

	function finishFlowingBlock($endofblock = false, $next = '')
	{
		$currentx = $this->x;
		//prints out the last chunk
		$is_table = $this->flowingBlockAttr['is_table'];
		$table_draft = $this->flowingBlockAttr['table_draft'];
		$maxWidth = & $this->flowingBlockAttr['width'];
		$stackHeight = & $this->flowingBlockAttr['height'];
		$align = & $this->flowingBlockAttr['align'];
		$content = & $this->flowingBlockAttr['content'];
		$contentB = & $this->flowingBlockAttr['contentB'];
		$font = & $this->flowingBlockAttr['font'];
		$contentWidth = & $this->flowingBlockAttr['contentWidth'];
		$lineCount = & $this->flowingBlockAttr['lineCount'];
		$valign = & $this->flowingBlockAttr['valign'];
		$blockstate = $this->flowingBlockAttr['blockstate'];

		$cOTLdata = & $this->flowingBlockAttr['cOTLdata']; // mPDF 5.7.1
		$newblock = $this->flowingBlockAttr['newblock'];
		$blockdir = $this->flowingBlockAttr['blockdir'];

		// *********** BLOCK BACKGROUND COLOR *****************//
		if ($this->blk[$this->blklvl]['bgcolor'] && !$is_table) {
			$fill = 0;
		} else {
			$this->SetFColor($this->ConvertColor(255));
			$fill = 0;
		}

		$hanger = '';
		// Always right trim!
		// Right trim last content and adjust width if needed to justify (later)
		if (isset($content[count($content) - 1]) && preg_match('/[ ]+$/', $content[count($content) - 1], $m)) {
			$strip = strlen($m[0]);
			$content[count($content) - 1] = substr($content[count($content) - 1], 0, (strlen($content[count($content) - 1]) - $strip));
			/* -- OTL -- */
			if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
				$this->otl->trimOTLdata($cOTLdata[count($cOTLdata) - 1], false, true);
			}
			/* -- END OTL -- */
		}

		// the amount of space taken up so far in user units
		$usedWidth = 0;

		// COLS
		$oldcolumn = $this->CurrCol;

		if ($this->ColActive && !$is_table) {
			$this->breakpoints[$this->CurrCol][] = $this->y;
		} // *COLUMNS*
		// Print out each chunk

		/* -- TABLES -- */
		if ($is_table) {
			$ipaddingL = 0;
			$ipaddingR = 0;
			$paddingL = 0;
			$paddingR = 0;
		} else {
			/* -- END TABLES -- */
			$ipaddingL = $this->blk[$this->blklvl]['padding_left'];
			$ipaddingR = $this->blk[$this->blklvl]['padding_right'];
			$paddingL = ($ipaddingL * _MPDFK);
			$paddingR = ($ipaddingR * _MPDFK);
			$this->cMarginL = $this->blk[$this->blklvl]['border_left']['w'];
			$this->cMarginR = $this->blk[$this->blklvl]['border_right']['w'];

			// Added mPDF 3.0 Float DIV
			$fpaddingR = 0;
			$fpaddingL = 0;
			/* -- CSS-FLOAT -- */
			if (count($this->floatDivs)) {
				list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
				if ($r_exists) {
					$fpaddingR = $r_width;
				}
				if ($l_exists) {
					$fpaddingL = $l_width;
				}
			}
			/* -- END CSS-FLOAT -- */

			$usey = $this->y + 0.002;
			if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 0)) {
				$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
			}
			/* -- CSS-IMAGE-FLOAT -- */
			// If float exists at this level
			if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) {
				$fpaddingR += $this->floatmargins['R']['w'];
			}
			if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) {
				$fpaddingL += $this->floatmargins['L']['w'];
			}
			/* -- END CSS-IMAGE-FLOAT -- */
		} // *TABLES*


		$lineBox = array();

		$this->_setInlineBlockHeights($lineBox, $stackHeight, $content, $font, $is_table);

		if ($is_table && count($content) == 0) {
			$stackHeight = 0;
		}

		if ($table_draft) {
			$this->y += $stackHeight;
			$this->objectbuffer = array();
			return 0;
		}

		// While we're at it, check if contains cursive text
		// Change NBSP to SPACE.
		// Re-calculate contentWidth
		$contentWidth = 0;

		foreach ($content as $k => $chunk) {
			$this->restoreFont($font[$k], false);
			if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
				// Soft Hyphens chr(173)
				if (!$this->usingCoreFont) {
					/* -- OTL -- */
					// mPDF 5.7.1
					if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
						$this->otl->removeChar($chunk, $cOTLdata[$k], "\xc2\xad");
						$this->otl->replaceSpace($chunk, $cOTLdata[$k]);
						$content[$k] = $chunk;
					}
					/* -- END OTL -- */ else {  // *OTL*
						$content[$k] = $chunk = str_replace("\xc2\xad", '', $chunk);
						$content[$k] = $chunk = str_replace(chr(194) . chr(160), chr(32), $chunk);
					} // *OTL*
				} else if ($this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats') {
					$content[$k] = $chunk = str_replace(chr(173), '', $chunk);
					$content[$k] = $chunk = str_replace(chr(160), chr(32), $chunk);
				}
				$contentWidth += $this->GetStringWidth($chunk, true, (isset($cOTLdata[$k]) ? $cOTLdata[$k] : false), $this->textvar) * _MPDFK;
			} else if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]) {
				// LIST MARKERS	// mPDF 6  Lists
				if ($this->objectbuffer[$k]['type'] == 'image' && isset($this->objectbuffer[$k]['listmarker']) && $this->objectbuffer[$k]['listmarker'] && $this->objectbuffer[$k]['listmarkerposition'] == 'outside') {
					// do nothing
				} else {
					$contentWidth += $this->objectbuffer[$k]['OUTER-WIDTH'] * _MPDFK;
				}
			}
		}

		if (isset($font[count($font) - 1])) {
			$lastfontreqstyle = (isset($font[count($font) - 1]['ReqFontStyle']) ? $font[count($font) - 1]['ReqFontStyle'] : '');
			$lastfontstyle = (isset($font[count($font) - 1]['style']) ? $font[count($font) - 1]['style'] : '');
		} else {
			$lastfontreqstyle = null;
			$lastfontstyle = null;
		}
		if ($blockdir == 'ltr' && strpos($lastfontreqstyle, "I") !== false && strpos($lastfontstyle, "I") === false) { // Artificial italic
			$lastitalic = $this->FontSize * 0.15 * _MPDFK;
		} else {
			$lastitalic = 0;
		}

		// Get PAGEBREAK TO TEST for height including the bottom border/padding
		$check_h = max($this->divheight, $stackHeight);

		// This fixes a proven bug...
		if ($endofblock && $newblock && $blockstate == 0 && !$content) {
			$check_h = 0;
		}
		// but ? needs to fix potentially more widespread...
		//	if (!$content) {  $check_h = 0; }

		if ($this->blklvl > 0 && !$is_table) {
			if ($endofblock && $blockstate > 1) {
				if ($this->blk[$this->blklvl]['page_break_after_avoid']) {
					$check_h += $stackHeight;
				}
				$check_h += ($this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w']);
			}
			if (($newblock && ($blockstate == 1 || $blockstate == 3) && $lineCount == 0) || ($endofblock && $blockstate == 3 && $lineCount == 0)) {
				$check_h += ($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['border_top']['w']);
			}
		}

		// Force PAGE break if column height cannot take check-height
		if ($this->ColActive && $check_h > ($this->PageBreakTrigger - $this->y0)) {
			$this->SetCol($this->NbCol - 1);
		}

		// Avoid just border/background-color moved on to next page
		if ($endofblock && $blockstate > 1 && !$content) {
			$buff = $this->margBuffer;
		} else {
			$buff = 0;
		}


		// PAGEBREAK
		if (!$is_table && ($this->y + $check_h) > ($this->PageBreakTrigger + $buff) and ! $this->InFooter and $this->AcceptPageBreak()) {
			$bak_x = $this->x; //Current X position
			// WORD SPACING
			$ws = $this->ws; //Word Spacing
			$charspacing = $this->charspacing; //Character Spacing
			$this->ResetSpacing();

			$this->AddPage($this->CurOrientation);

			$this->x = $bak_x;
			// Added to correct for OddEven Margins
			$currentx += $this->MarginCorrection;
			$this->x += $this->MarginCorrection;

			// WORD SPACING
			$this->SetSpacing($charspacing, $ws);
		}


		/* -- COLUMNS -- */
		// COLS
		// COLUMN CHANGE
		if ($this->CurrCol != $oldcolumn) {
			$currentx += $this->ChangeColumn * ($this->ColWidth + $this->ColGap);
			$this->x += $this->ChangeColumn * ($this->ColWidth + $this->ColGap);
			$oldcolumn = $this->CurrCol;
		}


		if ($this->ColActive && !$is_table) {
			$this->breakpoints[$this->CurrCol][] = $this->y;
		}
		/* -- END COLUMNS -- */

		// TOP MARGIN
		if ($newblock && ($blockstate == 1 || $blockstate == 3) && ($this->blk[$this->blklvl]['margin_top']) && $lineCount == 0 && !$is_table) {
			$this->DivLn($this->blk[$this->blklvl]['margin_top'], $this->blklvl - 1, true, $this->blk[$this->blklvl]['margin_collapse']);
			if ($this->ColActive) {
				$this->breakpoints[$this->CurrCol][] = $this->y;
			} // *COLUMNS*
		}

		if ($newblock && ($blockstate == 1 || $blockstate == 3) && $lineCount == 0 && !$is_table) {
			$this->blk[$this->blklvl]['y0'] = $this->y;
			$this->blk[$this->blklvl]['startpage'] = $this->page;
			if ($this->blk[$this->blklvl]['float']) {
				$this->blk[$this->blklvl]['float_start_y'] = $this->y;
			}
			if ($this->ColActive) {
				$this->breakpoints[$this->CurrCol][] = $this->y;
			} // *COLUMNS*
		}

		// Paragraph INDENT
		$WidthCorrection = 0;
		if (($newblock) && ($blockstate == 1 || $blockstate == 3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && ($align != 'C')) {
			$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'], $this->blk[$this->blklvl]['inner_width'], $this->blk[$this->blklvl]['InlineProperties']['size'], false);  // mPDF 5.7.4
			$WidthCorrection = ($ti * _MPDFK);
		}


		// PADDING and BORDER spacing/fill
		if (($newblock) && ($blockstate == 1 || $blockstate == 3) && (($this->blk[$this->blklvl]['padding_top']) || ($this->blk[$this->blklvl]['border_top'])) && ($lineCount == 0) && (!$is_table)) {
			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'], -3, true, false, 1);
			if ($this->ColActive) {
				$this->breakpoints[$this->CurrCol][] = $this->y;
			} // *COLUMNS*
			$this->x = $currentx;
		}


		// Added mPDF 3.0 Float DIV
		$fpaddingR = 0;
		$fpaddingL = 0;
		/* -- CSS-FLOAT -- */
		if (count($this->floatDivs)) {
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
			if ($r_exists) {
				$fpaddingR = $r_width;
			}
			if ($l_exists) {
				$fpaddingL = $l_width;
			}
		}
		/* -- END CSS-FLOAT -- */

		$usey = $this->y + 0.002;
		if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 0)) {
			$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
		}
		/* -- CSS-IMAGE-FLOAT -- */
		// If float exists at this level
		if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) {
			$fpaddingR += $this->floatmargins['R']['w'];
		}
		if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) {
			$fpaddingL += $this->floatmargins['L']['w'];
		}
		/* -- END CSS-IMAGE-FLOAT -- */


		if ($content) {

			// In FinishFlowing Block no lines are justified as it is always last line
			// but if CJKorphan has allowed content width to go over max width, use J charspacing to compress line
			// JUSTIFICATION J - NOT!
			$nb_carac = 0;
			$nb_spaces = 0;
			$jcharspacing = 0;
			$jkashida = 0;
			$jws = 0;
			$inclCursive = false;
			$dottab = false;
			foreach ($content as $k => $chunk) {
				if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
					$nb_carac += mb_strlen($chunk, $this->mb_enc);
					$nb_spaces += mb_substr_count($chunk, ' ', $this->mb_enc);
					// mPDF 6
					// Use GPOS OTL
					$this->restoreFont($font[$k], false);
					if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
						if (isset($cOTLdata[$k]['group']) && $cOTLdata[$k]['group']) {
							$nb_marks = substr_count($cOTLdata[$k]['group'], 'M');
							$nb_carac -= $nb_marks;
						}
						if (preg_match("/([" . $this->pregCURSchars . "])/u", $chunk)) {
							$inclCursive = true;
						}
					}
				} else {
					$nb_carac ++;  // mPDF 6 allow spacing for inline object
					if ($this->objectbuffer[$k]['type'] == 'dottab') {
						$dottab = $this->objectbuffer[$k]['outdent'];
					}
				}
			}

			// DIRECTIONALITY RTL
			$chunkorder = range(0, count($content) - 1); // mPDF 6
			/* -- OTL -- */
			// mPDF 6
			if ($blockdir == 'rtl' || $this->biDirectional) {
				$this->otl->_bidiReorder($chunkorder, $content, $cOTLdata, $blockdir);
				// From this point on, $content and $cOTLdata may contain more elements (and re-ordered) compared to
				// $this->objectbuffer and $font ($chunkorder contains the mapping)
			}
			/* -- END OTL -- */

			// Remove any XAdvance from OTL data at end of line
			// And correct for XPlacement on last character
			// BIDI is applied
			foreach ($chunkorder AS $aord => $k) {
				if (count($cOTLdata)) {
					$this->restoreFont($font[$k], false);
					// ...FinishFlowingBlock...
					if ($aord == count($chunkorder) - 1 && isset($cOTLdata[$aord]['group'])) { // Last chunk on line
						$nGPOS = strlen($cOTLdata[$aord]['group']) - 1; // Last character
						if (isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL']) || isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'])) {
							if (isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'])) {
								$w = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'] * 1000 / $this->CurrentFont['unitsPerEm'];
							} else {
								$w = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'] * 1000 / $this->CurrentFont['unitsPerEm'];
							}
							$w *= ($this->FontSize / 1000);
							$contentWidth -= $w * _MPDFK;
							$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'] = 0;
							$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'] = 0;
						}

						// If last character has an XPlacement set, adjust width calculation, and add to XAdvance to account for it
						if (isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'])) {
							$w = -$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'] * 1000 / $this->CurrentFont['unitsPerEm'];
							$w *= ($this->FontSize / 1000);
							$contentWidth -= $w * _MPDFK;
							$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'] = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'];
							$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'] = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'];
						}
					}
				}
			}

			// if it's justified, we need to find the char/word spacing (or if orphans have allowed length of line to go over the maxwidth)
			// If "orphans" in fact is just a final space - ignore this
			$lastchar = mb_substr($content[(count($chunkorder) - 1)], mb_strlen($content[(count($chunkorder) - 1)], $this->mb_enc) - 1, 1, $this->mb_enc);
			if (preg_match("/[" . $this->CJKoverflow . "]/u", $lastchar)) {
				$CJKoverflow = true;
			} else {
				$CJKoverflow = false;
			}
			if ((((($contentWidth + $lastitalic) > $maxWidth) && ($content[(count($chunkorder) - 1)] != ' ') ) ||
				(!$endofblock && $align == 'J' && ($next == 'image' || $next == 'select' || $next == 'input' || $next == 'textarea' || ($next == 'br' && $this->justifyB4br)))) && !($CJKoverflow && $this->allowCJKoverflow)) {
				// WORD SPACING
				list($jcharspacing, $jws, $jkashida) = $this->GetJspacing($nb_carac, $nb_spaces, ($maxWidth - $lastitalic - $contentWidth - $WidthCorrection - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) )), $inclCursive, $cOTLdata);
			}
			/* -- CJK-FONTS -- */ else if ($this->checkCJK && $align == 'J' && $CJKoverflow && $this->allowCJKoverflow && $this->CJKforceend) {
				// force-end overhang
				$hanger = mb_substr($content[(count($chunkorder) - 1)], mb_strlen($content[(count($chunkorder) - 1)], $this->mb_enc) - 1, 1, $this->mb_enc);
				if (preg_match("/[" . $this->CJKoverflow . "]/u", $hanger)) {
					$content[(count($chunkorder) - 1)] = mb_substr($content[(count($chunkorder) - 1)], 0, mb_strlen($content[(count($chunkorder) - 1)], $this->mb_enc) - 1, $this->mb_enc);
					$this->restoreFont($font[$chunkorder[count($chunkorder) - 1]], false);
					$contentWidth -= $this->GetStringWidth($hanger) * _MPDFK;
					$nb_carac -= 1;
					list($jcharspacing, $jws, $jkashida) = $this->GetJspacing($nb_carac, $nb_spaces, ($maxWidth - $lastitalic - $contentWidth - $WidthCorrection - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) )), $inclCursive, $cOTLdata);
				}
			}
			/* -- END CJK-FONTS -- */

			// Check if will fit at word/char spacing of previous line - if so continue it
			// but only allow a maximum of $this->jSmaxWordLast and $this->jSmaxCharLast
			else if ($contentWidth < ($maxWidth - $lastitalic - $WidthCorrection - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK))) && !$this->fixedlSpacing) {
				if ($this->ws > $this->jSmaxWordLast) {
					$jws = $this->jSmaxWordLast;
				}
				if ($this->charspacing > $this->jSmaxCharLast) {
					$jcharspacing = $this->jSmaxCharLast;
				}
				$check = $maxWidth - $lastitalic - $WidthCorrection - $contentWidth - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) ) - ( $jcharspacing * $nb_carac) - ( $jws * $nb_spaces);
				if ($check <= 0) {
					$jcharspacing = 0;
					$jws = 0;
				}
			}

			$empty = $maxWidth - $lastitalic - $WidthCorrection - $contentWidth - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) );


			$empty -= ($jcharspacing * ($nb_carac - 1)); // mPDF 6 nb_carac MINUS 1
			$empty -= ($jws * $nb_spaces);
			$empty -= ($jkashida);

			$empty /= _MPDFK;

			if (!$is_table) {
				$this->maxPosR = max($this->maxPosR, ($this->w - $this->rMargin - $this->blk[$this->blklvl]['outer_right_margin'] - $empty));
				$this->maxPosL = min($this->maxPosL, ($this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'] + $empty));
			}

			$arraysize = count($chunkorder);

			$margins = ($this->cMarginL + $this->cMarginR) + ($ipaddingL + $ipaddingR + $fpaddingR + $fpaddingR );

			if (!$is_table) {
				$this->DivLn($stackHeight, $this->blklvl, false);
			} // false -> don't advance y

			$this->x = $currentx + $this->cMarginL + $ipaddingL + $fpaddingL;
			if ($dottab !== false && $blockdir == 'rtl') {
				$this->x -= $dottab;
			} else if ($align == 'R') {
				$this->x += $empty;
			} else if ($align == 'J' && $blockdir == 'rtl') {
				$this->x += $empty;
			} else if ($align == 'C') {
				$this->x += ($empty / 2);
			}

			// Paragraph INDENT
			$WidthCorrection = 0;
			if (($newblock) && ($blockstate == 1 || $blockstate == 3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && ($align != 'C')) {
				$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'], $this->blk[$this->blklvl]['inner_width'], $this->blk[$this->blklvl]['InlineProperties']['size'], false);  // mPDF 5.7.4
				if ($blockdir != 'rtl') {
					$this->x += $ti;
				} // mPDF 6
			}

			foreach ($chunkorder AS $aord => $k) { // mPDF 5.7
				$chunk = $content[$aord];
				if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]) {

					$xadj = $this->x - $this->objectbuffer[$k]['OUTER-X'];
					$this->objectbuffer[$k]['OUTER-X'] += $xadj;
					$this->objectbuffer[$k]['BORDER-X'] += $xadj;
					$this->objectbuffer[$k]['INNER-X'] += $xadj;

					if ($this->objectbuffer[$k]['type'] == 'listmarker') {
						$this->objectbuffer[$k]['lineBox'] = $lineBox[-1]; // Block element details for glyph-origin
					}
					$yadj = $this->y - $this->objectbuffer[$k]['OUTER-Y'];
					if ($this->objectbuffer[$k]['type'] == 'dottab') { // mPDF 6 DOTTAB
						$this->objectbuffer[$k]['lineBox'] = $lineBox[$k]; // element details for glyph-origin
					}
					if ($this->objectbuffer[$k]['type'] != 'dottab') { // mPDF 6 DOTTAB
						$yadj += $lineBox[$k]['top'];
					}
					$this->objectbuffer[$k]['OUTER-Y'] += $yadj;
					$this->objectbuffer[$k]['BORDER-Y'] += $yadj;
					$this->objectbuffer[$k]['INNER-Y'] += $yadj;
				}

				$this->restoreFont($font[$k]);  // mPDF 5.7

				if ($is_table && substr($align, 0, 1) == 'D' && $aord == 0) {
					$dp = $this->decimal_align[substr($align, 0, 2)];
					$s = preg_split('/' . preg_quote($dp, '/') . '/', $content[0], 2);  // ? needs to be /u if not core
					$s0 = $this->GetStringWidth($s[0], false);
					$this->x += ($this->decimal_offset - $s0);
				}

				$this->SetSpacing(($this->fixedlSpacing * _MPDFK) + $jcharspacing, ($this->fixedlSpacing + $this->minwSpacing) * _MPDFK + $jws);
				$this->fixedlSpacing = false;
				$this->minwSpacing = 0;

				$save_vis = $this->visibility;
				if (isset($this->textparam['visibility']) && $this->textparam['visibility'] && $this->textparam['visibility'] != $this->visibility) {
					$this->SetVisibility($this->textparam['visibility']);
				}

				// *********** SPAN BACKGROUND COLOR ***************** //
				if (isset($this->spanbgcolor) && $this->spanbgcolor) {
					$cor = $this->spanbgcolorarray;
					$this->SetFColor($cor);
					$save_fill = $fill;
					$spanfill = 1;
					$fill = 1;
				}
				if (!empty($this->spanborddet)) {
					if (strpos($contentB[$k], 'L') !== false && isset($this->spanborddet['L']))
						$this->x += $this->spanborddet['L']['w'];
					if (strpos($contentB[$k], 'L') === false)
						$this->spanborddet['L']['s'] = $this->spanborddet['L']['w'] = 0;
					if (strpos($contentB[$k], 'R') === false)
						$this->spanborddet['R']['s'] = $this->spanborddet['R']['w'] = 0;
				}
				// WORD SPACING
				// mPDF 5.7.1
				$stringWidth = $this->GetStringWidth($chunk, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar);
				$nch = mb_strlen($chunk, $this->mb_enc);
				// Use GPOS OTL
				if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
					if (isset($cOTLdata[$aord]['group']) && $cOTLdata[$aord]['group']) {
						$nch -= substr_count($cOTLdata[$aord]['group'], 'M');
					}
				}
				$stringWidth += ( $this->charspacing * $nch / _MPDFK );

				$stringWidth += ( $this->ws * mb_substr_count($chunk, ' ', $this->mb_enc) / _MPDFK );

				if (isset($this->objectbuffer[$k])) {
					if ($this->objectbuffer[$k]['type'] == 'dottab') {
						$this->objectbuffer[$k]['OUTER-WIDTH'] +=$empty;
						$this->objectbuffer[$k]['OUTER-WIDTH'] +=$this->objectbuffer[$k]['outdent'];
					}
					// LIST MARKERS	// mPDF 6  Lists
					if ($this->objectbuffer[$k]['type'] == 'image' && isset($this->objectbuffer[$k]['listmarker']) && $this->objectbuffer[$k]['listmarker'] && $this->objectbuffer[$k]['listmarkerposition'] == 'outside') {
						// do nothing
					} else {
						$stringWidth = $this->objectbuffer[$k]['OUTER-WIDTH'];
					}
				}

				if ($stringWidth == 0) {
					$stringWidth = 0.000001;
				}
				if ($aord == $arraysize - 1) { // mPDF 5.7
					// mPDF 5.7.1
					if ($this->checkCJK && $CJKoverflow && $align == 'J' && $this->allowCJKoverflow && $hanger && $this->CJKforceend) {
						// force-end overhang
						$this->Cell($stringWidth, $stackHeight, $chunk, '', 0, '', $fill, $this->HREF, $currentx, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false));  // mPDF 5.7.1
						$this->Cell($this->GetStringWidth($hanger), $stackHeight, $hanger, '', 1, '', $fill, $this->HREF, $currentx, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false)); // mPDF 5.7.1
					} else {
						$this->Cell($stringWidth, $stackHeight, $chunk, '', 1, '', $fill, $this->HREF, $currentx, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false)); // mPDF 5.7.1
					}
				} else
					$this->Cell($stringWidth, $stackHeight, $chunk, '', 0, '', $fill, $this->HREF, 0, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false)); //first or middle part	// mPDF 5.7.1


				if (!empty($this->spanborddet)) {
					if (strpos($contentB[$k], 'R') !== false && $aord != $arraysize - 1)
						$this->x += $this->spanborddet['R']['w'];
				}
				// *********** SPAN BACKGROUND COLOR OFF - RESET BLOCK BGCOLOR ***************** //
				if (isset($spanfill) && $spanfill) {
					$fill = $save_fill;
					$spanfill = 0;
					if ($fill) {
						$this->SetFColor($bcor);
					}
				}
				if (isset($this->textparam['visibility']) && $this->textparam['visibility'] && $this->visibility != $save_vis) {
					$this->SetVisibility($save_vis);
				}
			}

			$this->printobjectbuffer($is_table, $blockdir);
			$this->objectbuffer = array();
			$this->ResetSpacing();
		} // END IF CONTENT

		/* -- CSS-IMAGE-FLOAT -- */
		// Update values if set to skipline
		if ($this->floatmargins) {
			$this->_advanceFloatMargins();
		}


		if ($endofblock && $blockstate > 1) {
			// If float exists at this level
			if (isset($this->floatmargins['R']['y1'])) {
				$fry1 = $this->floatmargins['R']['y1'];
			} else {
				$fry1 = 0;
			}
			if (isset($this->floatmargins['L']['y1'])) {
				$fly1 = $this->floatmargins['L']['y1'];
			} else {
				$fly1 = 0;
			}
			if ($this->y < $fry1 || $this->y < $fly1) {
				$drop = max($fry1, $fly1) - $this->y;
				$this->DivLn($drop);
				$this->x = $currentx;
			}
		}
		/* -- END CSS-IMAGE-FLOAT -- */


		// PADDING and BORDER spacing/fill
		if ($endofblock && ($blockstate > 1) && ($this->blk[$this->blklvl]['padding_bottom'] || $this->blk[$this->blklvl]['border_bottom'] || $this->blk[$this->blklvl]['css_set_height']) && (!$is_table)) {
			// If CSS height set, extend bottom - if on same page as block started, and CSS HEIGHT > actual height,
			// and does not force pagebreak
			$extra = 0;
			if (isset($this->blk[$this->blklvl]['css_set_height']) && $this->blk[$this->blklvl]['css_set_height'] && $this->blk[$this->blklvl]['startpage'] == $this->page) {
				// predicted height
				$h1 = ($this->y - $this->blk[$this->blklvl]['y0']) + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'];
				if ($h1 < ($this->blk[$this->blklvl]['css_set_height'] + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['padding_top'])) {
					$extra = ($this->blk[$this->blklvl]['css_set_height'] + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['padding_top']) - $h1;
				}
				if ($this->y + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $extra > $this->PageBreakTrigger) {
					$extra = $this->PageBreakTrigger - ($this->y + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w']);
				}
			}

			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $extra, -3, true, false, 2);
			$this->x = $currentx;

			if ($this->ColActive) {
				$this->breakpoints[$this->CurrCol][] = $this->y;
			} // *COLUMNS*
		}

		// SET Bottom y1 of block (used for painting borders)
		if (($endofblock) && ($blockstate > 1) && (!$is_table)) {
			$this->blk[$this->blklvl]['y1'] = $this->y;
		}

		// BOTTOM MARGIN
		if (($endofblock) && ($blockstate > 1) && ($this->blk[$this->blklvl]['margin_bottom']) && (!$is_table)) {
			if ($this->y + $this->blk[$this->blklvl]['margin_bottom'] < $this->PageBreakTrigger and ! $this->InFooter) {
				$this->DivLn($this->blk[$this->blklvl]['margin_bottom'], $this->blklvl - 1, true, $this->blk[$this->blklvl]['margin_collapse']);
				if ($this->ColActive) {
					$this->breakpoints[$this->CurrCol][] = $this->y;
				} // *COLUMNS*
			}
		}

		// Reset lineheight
		$stackHeight = $this->divheight;
	}

	function printobjectbuffer($is_table = false, $blockdir = false)
	{
		if (!$blockdir) {
			$blockdir = $this->directionality;
		}
		if ($is_table && $this->shrin_k > 1) {
			$k = $this->shrin_k;
		} else {
			$k = 1;
		}
		$save_y = $this->y;
		$save_x = $this->x;
		$save_currentfontfamily = $this->FontFamily;
		$save_currentfontsize = $this->FontSizePt;
		$save_currentfontstyle = $this->FontStyle;
		if ($blockdir == 'rtl') {
			$rtlalign = 'R';
		} else {
			$rtlalign = 'L';
		}
		foreach ($this->objectbuffer AS $ib => $objattr) {
			if ($objattr['type'] == 'bookmark' || $objattr['type'] == 'indexentry' || $objattr['type'] == 'toc') {
				$x = $objattr['OUTER-X'];
				$y = $objattr['OUTER-Y'];
				$this->y = $y - $this->FontSize / 2;
				$this->x = $x;
				if ($objattr['type'] == 'bookmark') {
					$this->Bookmark($objattr['CONTENT'], $objattr['bklevel'], $y - $this->FontSize);
				} // *BOOKMARKS*
				if ($objattr['type'] == 'indexentry') {
					$this->IndexEntry($objattr['CONTENT']);
				} // *INDEX*
				if ($objattr['type'] == 'toc') {
					$this->TOC_Entry($objattr['CONTENT'], $objattr['toclevel'], (isset($objattr['toc_id']) ? $objattr['toc_id'] : ''));
				} // *TOC*
			}
			/* -- ANNOTATIONS -- */ else if ($objattr['type'] == 'annot') {
				if ($objattr['POS-X']) {
					$x = $objattr['POS-X'];
				} else if ($this->annotMargin <> 0) {
					$x = -$objattr['OUTER-X'];
				} else {
					$x = $objattr['OUTER-X'];
				}
				if ($objattr['POS-Y']) {
					$y = $objattr['POS-Y'];
				} else {
					$y = $objattr['OUTER-Y'] - $this->FontSize / 2;
				}
				// Create a dummy entry in the _out/columnBuffer with position sensitive data,
				// linking $y-1 in the Columnbuffer with entry in $this->columnAnnots
				// and when columns are split in length will not break annotation from current line
				$this->y = $y - 1;
				$this->x = $x - 1;
				$this->Line($x - 1, $y - 1, $x - 1, $y - 1);
				$this->Annotation($objattr['CONTENT'], $x, $y, $objattr['ICON'], $objattr['AUTHOR'], $objattr['SUBJECT'], $objattr['OPACITY'], $objattr['COLOR'], (isset($objattr['POPUP']) ? $objattr['POPUP'] : ''), (isset($objattr['FILE']) ? $objattr['FILE'] : ''));
			}
			/* -- END ANNOTATIONS -- */ else {
				$y = $objattr['OUTER-Y'];
				$x = $objattr['OUTER-X'];
				$w = $objattr['OUTER-WIDTH'];
				$h = $objattr['OUTER-HEIGHT'];
				if (isset($objattr['text'])) {
					$texto = $objattr['text'];
				}
				$this->y = $y;
				$this->x = $x;
				if (isset($objattr['fontfamily'])) {
					$this->SetFont($objattr['fontfamily'], '', $objattr['fontsize']);
				}
			}

			// HR
			if ($objattr['type'] == 'hr') {
				$this->SetDColor($objattr['color']);
				switch ($objattr['align']) {
					case 'C':
						$empty = $objattr['OUTER-WIDTH'] - $objattr['INNER-WIDTH'];
						$empty /= 2;
						$x += $empty;
						break;
					case 'R':
						$empty = $objattr['OUTER-WIDTH'] - $objattr['INNER-WIDTH'];
						$x += $empty;
						break;
				}
				$oldlinewidth = $this->LineWidth;
				$this->SetLineWidth($objattr['linewidth'] / $k);
				$this->y += ($objattr['linewidth'] / 2) + $objattr['margin_top'] / $k;
				$this->Line($x, $this->y, $x + $objattr['INNER-WIDTH'], $this->y);
				$this->SetLineWidth($oldlinewidth);
				$this->SetDColor($this->ConvertColor(0));
			}
			// IMAGE
			if ($objattr['type'] == 'image') {
				// mPDF 5.7.3 TRANSFORMS
				if (isset($objattr['transform'])) {
					$this->_out("\n" . '% BTR'); // Begin Transform
				}
				if (isset($objattr['z-index']) && $objattr['z-index'] > 0 && $this->current_layer == 0) {
					$this->BeginLayer($objattr['z-index']);
				}
				if (isset($objattr['visibility']) && $objattr['visibility'] != 'visible' && $objattr['visibility']) {
					$this->SetVisibility($objattr['visibility']);
				}
				if (isset($objattr['opacity'])) {
					$this->SetAlpha($objattr['opacity']);
				}

				$obiw = $objattr['INNER-WIDTH'];
				$obih = $objattr['INNER-HEIGHT'];
				$sx = $objattr['INNER-WIDTH'] * _MPDFK / $objattr['orig_w'];
				$sy = abs($objattr['INNER-HEIGHT']) * _MPDFK / abs($objattr['orig_h']);
				$sx = ($objattr['INNER-WIDTH'] * _MPDFK / $objattr['orig_w']);
				$sy = ($objattr['INNER-HEIGHT'] * _MPDFK / $objattr['orig_h']);

				$rotate = 0;
				if (isset($objattr['ROTATE'])) {
					$rotate = $objattr['ROTATE'];
				}
				if ($rotate == 90) {
					// Clockwise
					$obiw = $objattr['INNER-HEIGHT'];
					$obih = $objattr['INNER-WIDTH'];
					$tr = $this->transformTranslate(0, -$objattr['INNER-WIDTH'], true);
					$tr .= ' ' . $this->transformRotate(90, $objattr['INNER-X'], ($objattr['INNER-Y'] + $objattr['INNER-WIDTH']), true);
					$sx = $obiw * _MPDFK / $objattr['orig_h'];
					$sy = $obih * _MPDFK / $objattr['orig_w'];
				} else if ($rotate == -90 || $rotate == 270) {
					// AntiClockwise
					$obiw = $objattr['INNER-HEIGHT'];
					$obih = $objattr['INNER-WIDTH'];
					$tr = $this->transformTranslate($objattr['INNER-WIDTH'], ($objattr['INNER-HEIGHT'] - $objattr['INNER-WIDTH']), true);
					$tr .= ' ' . $this->transformRotate(-90, $objattr['INNER-X'], ($objattr['INNER-Y'] + $objattr['INNER-WIDTH']), true);
					$sx = $obiw * _MPDFK / $objattr['orig_h'];
					$sy = $obih * _MPDFK / $objattr['orig_w'];
				} else if ($rotate == 180) {
					// Mirror
					$tr = $this->transformTranslate($objattr['INNER-WIDTH'], -$objattr['INNER-HEIGHT'], true);
					$tr .= ' ' . $this->transformRotate(180, $objattr['INNER-X'], ($objattr['INNER-Y'] + $objattr['INNER-HEIGHT']), true);
				} else {
					$tr = '';
				}
				$tr = trim($tr);
				if ($tr) {
					$tr .= ' ';
				}
				$gradmask = '';

				// mPDF 5.7.3 TRANSFORMS
				$tr2 = '';
				if (isset($objattr['transform'])) {
					$maxsize_x = $w;
					$maxsize_y = $h;
					$cx = $x + $w / 2;
					$cy = $y + $h / 2;
					preg_match_all('/(translatex|translatey|translate|scalex|scaley|scale|rotate|skewX|skewY|skew)\((.*?)\)/is', $objattr['transform'], $m);
					if (count($m[0])) {
						for ($i = 0; $i < count($m[0]); $i++) {
							$c = strtolower($m[1][$i]);
							$v = trim($m[2][$i]);
							$vv = preg_split('/[ ,]+/', $v);
							if ($c == 'translate' && count($vv)) {
								$translate_x = $this->ConvertSize($vv[0], $maxsize_x, false, false);
								if (count($vv) == 2) {
									$translate_y = $this->ConvertSize($vv[1], $maxsize_y, false, false);
								} else {
									$translate_y = 0;
								}
								$tr2 .= $this->transformTranslate($translate_x, $translate_y, true) . ' ';
							} else if ($c == 'translatex' && count($vv)) {
								$translate_x = $this->ConvertSize($vv[0], $maxsize_x, false, false);
								$tr2 .= $this->transformTranslate($translate_x, 0, true) . ' ';
							} else if ($c == 'translatey' && count($vv)) {
								$translate_y = $this->ConvertSize($vv[1], $maxsize_y, false, false);
								$tr2 .= $this->transformTranslate(0, $translate_y, true) . ' ';
							} else if ($c == 'scale' && count($vv)) {
								$scale_x = $vv[0] * 100;
								if (count($vv) == 2) {
									$scale_y = $vv[1] * 100;
								} else {
									$scale_y = $scale_x;
								}
								$tr2 .= $this->transformScale($scale_x, $scale_y, $cx, $cy, true) . ' ';
							} else if ($c == 'scalex' && count($vv)) {
								$scale_x = $vv[0] * 100;
								$tr2 .= $this->transformScale($scale_x, 0, $cx, $cy, true) . ' ';
							} else if ($c == 'scaley' && count($vv)) {
								$scale_y = $vv[1] * 100;
								$tr2 .= $this->transformScale(0, $scale_y, $cx, $cy, true) . ' ';
							} else if ($c == 'skew' && count($vv)) {
								$angle_x = $this->ConvertAngle($vv[0], false);
								if (count($vv) == 2) {
									$angle_y = $this->ConvertAngle($vv[1], false);
								} else {
									$angle_y = 0;
								}
								$tr2 .= $this->transformSkew($angle_x, $angle_y, $cx, $cy, true) . ' ';
							} else if ($c == 'skewx' && count($vv)) {
								$angle = $this->ConvertAngle($vv[0], false);
								$tr2 .= $this->transformSkew($angle, 0, $cx, $cy, true) . ' ';
							} else if ($c == 'skewy' && count($vv)) {
								$angle = $this->ConvertAngle($vv[0], false);
								$tr2 .= $this->transformSkew(0, $angle, $cx, $cy, true) . ' ';
							} else if ($c == 'rotate' && count($vv)) {
								$angle = $this->ConvertAngle($vv[0]);
								$tr2 .= $this->transformRotate($angle, $cx, $cy, true) . ' ';
							}
						}
					}
				}

				// LIST MARKERS (Images)	// mPDF 6  Lists
				if (isset($objattr['listmarker']) && $objattr['listmarker'] && $objattr['listmarkerposition'] == 'outside') {
					$mw = $objattr['OUTER-WIDTH'];
					//  NB If change marker-offset, also need to alter in function _getListMarkerWidth
					$adjx = $this->ConvertSize($this->list_marker_offset, $this->FontSize);
					if ($objattr['dir'] == 'rtl') {
						$objattr['INNER-X'] += $adjx;
					} else {
						$objattr['INNER-X'] -= $adjx;
						$objattr['INNER-X'] -= $mw;
					}
				}
				// mPDF 5.7.3 TRANSFORMS / BACKGROUND COLOR
				// Transform also affects image background
				if ($tr2) {
					$this->_out('q ' . $tr2 . ' ');
				}
				if (isset($objattr['bgcolor']) && $objattr['bgcolor']) {
					$bgcol = $objattr['bgcolor'];
					$this->SetFColor($bgcol);
					$this->Rect($x, $y, $w, $h, 'F');
					$this->SetFColor($this->ConvertColor(255));
				}
				if ($tr2) {
					$this->_out('Q');
				}

				/* -- BACKGROUNDS -- */
				if (isset($objattr['GRADIENT-MASK'])) {
					$g = $this->grad->parseMozGradient($objattr['GRADIENT-MASK']);
					if ($g) {
						$dummy = $this->grad->Gradient($objattr['INNER-X'], $objattr['INNER-Y'], $obiw, $obih, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true, true);
						$gradmask = '/TGS' . count($this->gradients) . ' gs ';
					}
				}
				/* -- END BACKGROUNDS -- */
				/* -- IMAGES-WMF -- */
				if (isset($objattr['itype']) && $objattr['itype'] == 'wmf') {
					$outstring = sprintf('q ' . $tr . $tr2 . '%.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, -$sy, $objattr['INNER-X'] * _MPDFK - $sx * $objattr['wmf_x'], (($this->h - $objattr['INNER-Y']) * _MPDFK) + $sy * $objattr['wmf_y'], $objattr['ID']); // mPDF 5.7.3 TRANSFORMS
				} else
				/* -- END IMAGES-WMF -- */
				if (isset($objattr['itype']) && $objattr['itype'] == 'svg') {
					$outstring = sprintf('q ' . $tr . $tr2 . '%.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, -$sy, $objattr['INNER-X'] * _MPDFK - $sx * $objattr['wmf_x'], (($this->h - $objattr['INNER-Y']) * _MPDFK) + $sy * $objattr['wmf_y'], $objattr['ID']); // mPDF 5.7.3 TRANSFORMS
				} else {
					$outstring = sprintf("q " . $tr . $tr2 . "%.3F 0 0 %.3F %.3F %.3F cm " . $gradmask . "/I%d Do Q", $obiw * _MPDFK, $obih * _MPDFK, $objattr['INNER-X'] * _MPDFK, ($this->h - ($objattr['INNER-Y'] + $obih )) * _MPDFK, $objattr['ID']); // mPDF 5.7.3 TRANSFORMS
				}
				$this->_out($outstring);
				// LINK
				if (isset($objattr['link']))
					$this->Link($objattr['INNER-X'], $objattr['INNER-Y'], $objattr['INNER-WIDTH'], $objattr['INNER-HEIGHT'], $objattr['link']);
				if (isset($objattr['opacity'])) {
					$this->SetAlpha(1);
				}

				// mPDF 5.7.3 TRANSFORMS
				// Transform also affects image borders
				if ($tr2) {
					$this->_out('q ' . $tr2 . ' ');
				}
				if ((isset($objattr['border_top']) && $objattr['border_top'] > 0) || (isset($objattr['border_left']) && $objattr['border_left'] > 0) || (isset($objattr['border_right']) && $objattr['border_right'] > 0) || (isset($objattr['border_bottom']) && $objattr['border_bottom'] > 0)) {
					$this->PaintImgBorder($objattr, $is_table);
				}
				if ($tr2) {
					$this->_out('Q');
				}

				if (isset($objattr['visibility']) && $objattr['visibility'] != 'visible' && $objattr['visibility']) {
					$this->SetVisibility('visible');
				}
				if (isset($objattr['z-index']) && $objattr['z-index'] > 0 && $this->current_layer == 0) {
					$this->EndLayer();
				}
				// mPDF 5.7.3 TRANSFORMS
				if (isset($objattr['transform'])) {
					$this->_out("\n" . '% ETR'); // End Transform
				}
			}

			

			// TEXT CIRCLE
			if ($objattr['type'] == 'textcircle') {
				$bgcol = '';
				if (isset($objattr['bgcolor']) && $objattr['bgcolor']) {
					$bgcol = $objattr['bgcolor'];
				}
				$col = $this->ConvertColor(0);
				if (isset($objattr['color']) && $objattr['color']) {
					$col = $objattr['color'];
				}
				$this->SetTColor($col);
				$this->SetFColor($bgcol);
				if ($bgcol)
					$this->Rect($objattr['BORDER-X'], $objattr['BORDER-Y'], $objattr['BORDER-WIDTH'], $objattr['BORDER-HEIGHT'], 'F');
				$this->SetFColor($this->ConvertColor(255));
				if (isset($objattr['BORDER-WIDTH'])) {
					$this->PaintImgBorder($objattr, $is_table);
				}
				if (!class_exists('directw', false)) {
					include(_MPDF_PATH . 'classes/directw.php');
				}
				if (empty($this->directw)) {
					$this->directw = new directw($this);
				}
				if (isset($objattr['top-text'])) {
					$this->directw->CircularText($objattr['INNER-X'] + $objattr['INNER-WIDTH'] / 2, $objattr['INNER-Y'] + $objattr['INNER-HEIGHT'] / 2, $objattr['r'] / $k, $objattr['top-text'], 'top', $objattr['fontfamily'], $objattr['fontsize'] / $k, $objattr['fontstyle'], $objattr['space-width'], $objattr['char-width'], (isset($objattr['divider']) ? $objattr['divider'] : ''));
				}
				if (isset($objattr['bottom-text'])) {
					$this->directw->CircularText($objattr['INNER-X'] + $objattr['INNER-WIDTH'] / 2, $objattr['INNER-Y'] + $objattr['INNER-HEIGHT'] / 2, $objattr['r'] / $k, $objattr['bottom-text'], 'bottom', $objattr['fontfamily'], $objattr['fontsize'] / $k, $objattr['fontstyle'], $objattr['space-width'], $objattr['char-width'], (isset($objattr['divider']) ? $objattr['divider'] : ''));
				}
			}

			$this->ResetSpacing();

			// LIST MARKERS (Text or bullets)	// mPDF 6  Lists
			if ($objattr['type'] == 'listmarker') {
				if (isset($objattr['fontfamily'])) {
					$this->SetFont($objattr['fontfamily'], $objattr['fontstyle'], $objattr['fontsizept']);
				}
				$col = $this->ConvertColor(0);
				if (isset($objattr['colorarray']) && ($objattr['colorarray'])) {
					$col = $objattr['colorarray'];
				}

				if (isset($objattr['bullet']) && $objattr['bullet']) { // Used for position "outside" only
					$type = $objattr['bullet'];
					$size = $objattr['size'];

					if ($objattr['listmarkerposition'] == 'inside') {
						$adjx = $size / 2;
						if ($objattr['dir'] == 'rtl') {
							$adjx += $objattr['offset'];
						}
						$this->x += $adjx;
					} else {
						$adjx = $objattr['offset'];
						$adjx += $size / 2;
						if ($objattr['dir'] == 'rtl') {
							$this->x += $adjx;
						} else {
							$this->x -= $adjx;
						}
					}

					$yadj = $objattr['lineBox']['glyphYorigin'];
					if (isset($this->CurrentFont['desc']['XHeight']) && $this->CurrentFont['desc']['XHeight']) {
						$xh = $this->CurrentFont['desc']['XHeight'];
					} else {
						$xh = 500;
					}
					$yadj -= ($this->FontSize * $xh / 1000) * 0.625; // Vertical height of bullet (centre) from baseline= XHeight * 0.625
					$this->y += $yadj;

					$this->_printListBullet($this->x, $this->y, $size, $type, $col);
				} else {
					$this->SetTColor($col);
					$w = $this->GetStringWidth($texto);
					//  NB If change marker-offset, also need to alter in function _getListMarkerWidth
					$adjx = $this->ConvertSize($this->list_marker_offset, $this->FontSize);
					if ($objattr['dir'] == 'rtl') {
						$align = 'L';
						$this->x += $adjx;
					} else {
						// Use these lines to set as marker-offset, right-aligned - default
						$align = 'R';
						$this->x -= $adjx;
						$this->x -= $w;
					}
					$this->Cell($w, $this->FontSize, $texto, 0, 0, $align, 0, '', 0, 0, 0, 'T', 0, false, false, 0, $objattr['lineBox']);
					$this->SetTColor($this->ConvertColor(0));
				}
			}

			// DOT-TAB
			if ($objattr['type'] == 'dottab') {
				if (isset($objattr['fontfamily'])) {
					$this->SetFont($objattr['fontfamily'], '', $objattr['fontsize']);
				}
				$sp = $this->GetStringWidth(' ');
				$nb = floor(($w - 2 * $sp) / $this->GetStringWidth('.'));
				if ($nb > 0) {
					$dots = ' ' . str_repeat('.', $nb) . ' ';
				} else {
					$dots = ' ';
				}
				$col = $this->ConvertColor(0);
				if (isset($objattr['colorarray']) && ($objattr['colorarray'])) {
					$col = $objattr['colorarray'];
				}
				$this->SetTColor($col);
				$save_dh = $this->divheight;
				$save_sbd = $this->spanborddet;
				$save_textvar = $this->textvar; // mPDF 5.7.1
				$this->spanborddet = '';
				$this->divheight = 0;
				$this->textvar = 0x00; // mPDF 5.7.1

				$this->Cell($w, $h, $dots, 0, 0, 'C', 0, '', 0, 0, 0, 'T', 0, false, false, 0, $objattr['lineBox']); // mPDF 6 DOTTAB
				$this->spanborddet = $save_sbd;
				$this->textvar = $save_textvar; // mPDF 5.7.1
				$this->divheight = $save_dh;
				$this->SetTColor($this->ConvertColor(0));
			}

			
		}
		$this->SetFont($save_currentfontfamily, $save_currentfontstyle, $save_currentfontsize);
		$this->y = $save_y;
		$this->x = $save_x;
		unset($content);
	}

	

// mPDF 6
// Get previous character and move pointers
	function _moveToPrevChar(&$contentctr, &$charctr, $content)
	{
		$lastchar = false;
		$charctr--;
		while ($charctr < 0) { // go back to previous $content[]
			$contentctr--;
			if ($contentctr < 0) {
				return false;
			}
			if ($this->usingCoreFont) {
				$charctr = strlen($content[$contentctr]) - 1;
			} else {
				$charctr = mb_strlen($content[$contentctr], $this->mb_enc) - 1;
			}
		}
		if ($this->usingCoreFont) {
			$lastchar = $content[$contentctr][$charctr];
		} else {
			$lastchar = mb_substr($content[$contentctr], $charctr, 1, $this->mb_enc);
		}
		return $lastchar;
	}

// Get previous character
	function _getPrevChar($contentctr, $charctr, $content)
	{
		$lastchar = false;
		$charctr--;
		while ($charctr < 0) { // go back to previous $content[]
			$contentctr--;
			if ($contentctr < 0) {
				return false;
			}
			if ($this->usingCoreFont) {
				$charctr = strlen($content[$contentctr]) - 1;
			} else {
				$charctr = mb_strlen($content[$contentctr], $this->mb_enc) - 1;
			}
		}
		if ($this->usingCoreFont) {
			$lastchar = $content[$contentctr][$charctr];
		} else {
			$lastchar = mb_substr($content[$contentctr], $charctr, 1, $this->mb_enc);
		}
		return $lastchar;
	}

	function WriteFlowingBlock($s, $sOTLdata)
	{ // mPDF 5.7.1
		$currentx = $this->x;
		$is_table = $this->flowingBlockAttr['is_table'];
		$table_draft = $this->flowingBlockAttr['table_draft'];
		// width of all the content so far in points
		$contentWidth = & $this->flowingBlockAttr['contentWidth'];
		// cell width in points
		$maxWidth = & $this->flowingBlockAttr['width'];
		$lineCount = & $this->flowingBlockAttr['lineCount'];
		// line height in user units
		$stackHeight = & $this->flowingBlockAttr['height'];
		$align = & $this->flowingBlockAttr['align'];
		$content = & $this->flowingBlockAttr['content'];
		$contentB = & $this->flowingBlockAttr['contentB'];
		$font = & $this->flowingBlockAttr['font'];
		$valign = & $this->flowingBlockAttr['valign'];
		$blockstate = $this->flowingBlockAttr['blockstate'];
		$cOTLdata = & $this->flowingBlockAttr['cOTLdata']; // mPDF 5.7.1

		$newblock = $this->flowingBlockAttr['newblock'];
		$blockdir = $this->flowingBlockAttr['blockdir'];

		// *********** BLOCK BACKGROUND COLOR ***************** //
		if ($this->blk[$this->blklvl]['bgcolor'] && !$is_table) {
			$fill = 0;
		} else {
			$this->SetFColor($this->ConvertColor(255));
			$fill = 0;
		}
		$font[] = $this->saveFont();
		$content[] = '';
		$contentB[] = '';
		$cOTLdata[] = $sOTLdata; // mPDF 5.7.1
		$currContent = & $content[count($content) - 1];

		$CJKoverflow = false;
		$Oikomi = false; // mPDF 6
		$hanger = '';

		// COLS
		$oldcolumn = $this->CurrCol;
		if ($this->ColActive && !$is_table) {
			$this->breakpoints[$this->CurrCol][] = $this->y;
		} // *COLUMNS*

		/* -- TABLES -- */
		if ($is_table) {
			$ipaddingL = 0;
			$ipaddingR = 0;
			$paddingL = 0;
			$paddingR = 0;
			$cpaddingadjustL = 0;
			$cpaddingadjustR = 0;
			// Added mPDF 3.0
			$fpaddingR = 0;
			$fpaddingL = 0;
		} else {
			/* -- END TABLES -- */
			$ipaddingL = $this->blk[$this->blklvl]['padding_left'];
			$ipaddingR = $this->blk[$this->blklvl]['padding_right'];
			$paddingL = ($ipaddingL * _MPDFK);
			$paddingR = ($ipaddingR * _MPDFK);
			$this->cMarginL = $this->blk[$this->blklvl]['border_left']['w'];
			$cpaddingadjustL = -$this->cMarginL;
			$this->cMarginR = $this->blk[$this->blklvl]['border_right']['w'];
			$cpaddingadjustR = -$this->cMarginR;
			// Added mPDF 3.0 Float DIV
			$fpaddingR = 0;
			$fpaddingL = 0;
			/* -- CSS-FLOAT -- */
			if (count($this->floatDivs)) {
				list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
				if ($r_exists) {
					$fpaddingR = $r_width;
				}
				if ($l_exists) {
					$fpaddingL = $l_width;
				}
			}
			/* -- END CSS-FLOAT -- */

			$usey = $this->y + 0.002;
			if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 0)) {
				$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
			}
			/* -- CSS-IMAGE-FLOAT -- */
			// If float exists at this level
			if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) {
				$fpaddingR += $this->floatmargins['R']['w'];
			}
			if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) {
				$fpaddingL += $this->floatmargins['L']['w'];
			}
			/* -- END CSS-IMAGE-FLOAT -- */
		} // *TABLES*
		//OBJECTS - IMAGES & FORM Elements (NB has already skipped line/page if required - in printbuffer)
		if (substr($s, 0, 3) == "\xbb\xa4\xac") { //identifier has been identified!
			$objattr = $this->_getObjAttr($s);
			$h_corr = 0;
			if ($is_table) { // *TABLES*
				$maximumW = ($maxWidth / _MPDFK) - ($this->cellPaddingL + $this->cMarginL + $this->cellPaddingR + $this->cMarginR);  // *TABLES*
			} // *TABLES*
			else { // *TABLES*
				if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 0) && (!$is_table)) {
					$h_corr = $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
				}
				$maximumW = ($maxWidth / _MPDFK) - ($this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_right'] + $this->blk[$this->blklvl]['border_right']['w'] + $fpaddingL + $fpaddingR );
			} // *TABLES*
			$objattr = $this->inlineObject($objattr['type'], $this->lMargin + $fpaddingL + ($contentWidth / _MPDFK), ($this->y + $h_corr), $objattr, $this->lMargin, ($contentWidth / _MPDFK), $maximumW, $stackHeight, true, $is_table);

			// SET LINEHEIGHT for this line ================ RESET AT END
			$stackHeight = MAX($stackHeight, $objattr['OUTER-HEIGHT']);
			$this->objectbuffer[count($content) - 1] = $objattr;
			// if (isset($objattr['vertical-align'])) { $valign = $objattr['vertical-align']; }
			// else { $valign = ''; }
			// LIST MARKERS	// mPDF 6  Lists
			if ($objattr['type'] == 'image' && isset($objattr['listmarker']) && $objattr['listmarker'] && $objattr['listmarkerposition'] == 'outside') {
				// do nothing
			} else {
				$contentWidth += ($objattr['OUTER-WIDTH'] * _MPDFK);
			}
			return;
		}

		$lbw = $rbw = 0; // Border widths
		if (!empty($this->spanborddet)) {
			if (isset($this->spanborddet['L']))
				$lbw = $this->spanborddet['L']['w'];
			if (isset($this->spanborddet['R']))
				$rbw = $this->spanborddet['R']['w'];
		}

		if ($this->usingCoreFont) {
			$clen = strlen($s);
		} else {
			$clen = mb_strlen($s, $this->mb_enc);
		}

		// for every character in the string
		for ($i = 0; $i < $clen; $i++) {

			// extract the current character
			// get the width of the character in points
			if ($this->usingCoreFont) {
				$c = $s[$i];
				// Soft Hyphens chr(173)
				$cw = ($this->GetCharWidthCore($c) * _MPDFK);
				if (($this->textvar & FC_KERNING) && $i > 0) { // mPDF 5.7.1
					if (isset($this->CurrentFont['kerninfo'][$s[($i - 1)]][$c])) {
						$cw += ($this->CurrentFont['kerninfo'][$s[($i - 1)]][$c] * $this->FontSizePt / 1000 );
					}
				}
			} else {
				$c = mb_substr($s, $i, 1, $this->mb_enc);
				$cw = ($this->GetCharWidthNonCore($c, false) * _MPDFK);
				// mPDF 5.7.1
				// Use OTL GPOS
				if (isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0xFF)) {
					// ...WriteFlowingBlock...
					// Only  add XAdvanceL (not sure at present whether RTL or LTR writing direction)
					// At this point, XAdvanceL and XAdvanceR will balance
					if (isset($sOTLdata['GPOSinfo'][$i]['XAdvanceL'])) {
						$cw += $sOTLdata['GPOSinfo'][$i]['XAdvanceL'] * (1000 / $this->CurrentFont['unitsPerEm']) * ($this->FontSize / 1000) * _MPDFK;
					}
				}
				if (($this->textvar & FC_KERNING) && $i > 0) { // mPDF 5.7.1
					$lastc = mb_substr($s, ($i - 1), 1, $this->mb_enc);
					$ulastc = $this->UTF8StringToArray($lastc, false);
					$uc = $this->UTF8StringToArray($c, false);
					if (isset($this->CurrentFont['kerninfo'][$ulastc[0]][$uc[0]])) {
						$cw += ($this->CurrentFont['kerninfo'][$ulastc[0]][$uc[0]] * $this->FontSizePt / 1000 );
					}
				}
			}

			if ($i == 0) {
				$cw += $lbw * _MPDFK;
				$contentB[(count($contentB) - 1)] .= 'L';
			}
			if ($i == ($clen - 1)) {
				$cw += $rbw * _MPDFK;
				$contentB[(count($contentB) - 1)] .= 'R';
			}
			if ($c == ' ') {
				$currContent .= $c;
				$contentWidth += $cw;
				continue;
			}

			// Paragraph INDENT
			$WidthCorrection = 0;
			if (($newblock) && ($blockstate == 1 || $blockstate == 3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && ($align != 'C')) {
				$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'], $this->blk[$this->blklvl]['inner_width'], $this->blk[$this->blklvl]['InlineProperties']['size'], false);  // mPDF 5.7.4
				$WidthCorrection = ($ti * _MPDFK);
			}
			// OUTDENT
			foreach ($this->objectbuffer AS $k => $objattr) {   // mPDF 6 DOTTAB
				if ($objattr['type'] == 'dottab') {
					$WidthCorrection -= ($objattr['outdent'] * _MPDFK);
					break;
				}
			}


			// Added mPDF 3.0 Float DIV
			$fpaddingR = 0;
			$fpaddingL = 0;
			/* -- CSS-FLOAT -- */
			if (count($this->floatDivs)) {
				list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
				if ($r_exists) {
					$fpaddingR = $r_width;
				}
				if ($l_exists) {
					$fpaddingL = $l_width;
				}
			}
			/* -- END CSS-FLOAT -- */

			$usey = $this->y + 0.002;
			if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 0)) {
				$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
			}

			/* -- CSS-IMAGE-FLOAT -- */
			// If float exists at this level
			if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) {
				$fpaddingR += $this->floatmargins['R']['w'];
			}
			if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) {
				$fpaddingL += $this->floatmargins['L']['w'];
			}
			/* -- END CSS-IMAGE-FLOAT -- */


			// try adding another char
			if (( $contentWidth + $cw > $maxWidth - $WidthCorrection - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) ) + 0.001)) {// 0.001 is to correct for deviations converting mm=>pts
				// it won't fit, output what we already have
				$lineCount++;

				// contains any content that didn't make it into this print
				$savedContent = '';
				$savedContentB = '';
				$savedOTLdata = array(); // mPDF 5.7.1
				$savedFont = array();
				$savedObj = array();
				$savedPreOTLdata = array(); // mPDF 5.7.1
				$savedPreContent = array();
				$savedPreContentB = array();
				$savedPreFont = array();

				// mPDF 6
				// New line-breaking algorithm
				/////////////////////
				// LINE BREAKING
				/////////////////////
				$breakfound = false;
				$contentctr = count($content) - 1;
				if ($this->usingCoreFont) {
					$charctr = strlen($currContent);
				} else {
					$charctr = mb_strlen($currContent, $this->mb_enc);
				}
				$checkchar = $c;
				$prevchar = $this->_getPrevChar($contentctr, $charctr, $content);

				/* -- CJK-FONTS -- */
				/////////////////////
				// 1) CJK Overflowing a) punctuation or b) Oikomi
				/////////////////////
				// Next character ($c) is suitable to add as overhanging or squeezed punctuation, or Oikomi
				if ($CJKoverflow || $Oikomi) { // If flag already set
					$CJKoverflow = false;
					$Oikomi = false;
					$breakfound = true;
				}
				if (!$this->usingCoreFont && !$breakfound && $this->checkCJK) {
					// Get next/following character (in this chunk)
					$followingchar = '';
					if ($i < ($clen - 1)) {
						if ($this->usingCoreFont) {
							$followingchar = $s[$i + 1];
						} else {
							$followingchar = mb_substr($s, $i + 1, 1, $this->mb_enc);
						}
					}
					/////////////////////
					// 1a) Overflow punctuation
					/////////////////////
					if (preg_match("/[" . $this->pregCJKchars . "]/u", $prevchar) && preg_match("/[" . $this->CJKoverflow . "]/u", $checkchar) && $this->allowCJKorphans) {
						// add character onto this line
						$currContent .= $c;
						$contentWidth += $cw;
						$CJKoverflow = true; // Set flag
						continue;
					}
					/////////////////////
					// 1b) Try squeezing another character(s) onto this line = Oikomi, if character cannot end line
					//  or next character cannot start line (and not splitting CJK numerals)
					/////////////////////
					// NB otherwise it move lastchar(s) to next line to keep $c company = Oidashi, which is done below in standard way
					else if (preg_match("/[" . $this->pregCJKchars . "]/u", $checkchar) && $this->allowCJKorphans &&
						(preg_match("/[" . $this->CJKleading . "]/u", $followingchar) || preg_match("/[" . $this->CJKfollowing . "]/u", $checkchar)) &&
						!preg_match("/[" . $this->CJKleading . "]/u", $checkchar) && !preg_match("/[" . $this->CJKfollowing . "]/u", $followingchar) &&
						!(preg_match("/[0-9\x{ff10}-\x{ff19}]/u", $followingchar) && preg_match("/[0-9\x{ff10}-\x{ff19}]/u", $checkchar))) {
						// add character onto this line
						$currContent .= $c;
						$contentWidth += $cw;
						$Oikomi = true; // Set flag
						continue;
					}
				}
				/* -- END CJK-FONTS -- */
				/* -- HYPHENATION -- */
				/////////////////////
				// AUTOMATIC HYPHENATION
				// 2) Automatic hyphen in current word (does not cross tags)
				/////////////////////
				if (isset($this->textparam['hyphens']) && $this->textparam['hyphens'] == 1) {
					$currWord = '';
					// Look back and ahead to get current word
					for ($ac = $charctr - 1; $ac >= 0; $ac--) {
						if ($this->usingCoreFont) {
							$addc = substr($currContent, $ac, 1);
						} else {
							$addc = mb_substr($currContent, $ac, 1, $this->mb_enc);
						}
						if ($addc == ' ') {
							break;
						}
						$currWord = $addc . $currWord;
					}
					$start = $ac + 1;
					for ($ac = $i; $ac < ($clen - 1); $ac++) {
						if ($this->usingCoreFont) {
							$addc = substr($s, $ac, 1);
						} else {
							$addc = mb_substr($s, $ac, 1, $this->mb_enc);
						}
						if ($addc == ' ') {
							break;
						}
						$currWord .= $addc;
					}
					$ptr = $this->hyphenateWord($currWord, $charctr - $start);
					if ($ptr > -1) {
						$breakfound = array($contentctr, $start + $ptr, $contentctr, $start + $ptr, 'hyphen');
					}
				}
				/* -- END HYPHENATION -- */

				// Search backwards to find first line-break opportunity
				while ($breakfound == false && $prevchar !== false) {
					$cutcontentctr = $contentctr;
					$cutcharctr = $charctr;
					$prevchar = $this->_moveToPrevChar($contentctr, $charctr, $content);
					/////////////////////
					// 3) Break at SPACE
					/////////////////////
					if ($prevchar == ' ') {
						$breakfound = array($contentctr, $charctr, $cutcontentctr, $cutcharctr, 'discard');
					}
					/////////////////////
					// 4) Break at U+200B in current word (Khmer, Lao & Thai Invisible word boundary, and Tibetan)
					/////////////////////
					else if ($prevchar == "\xe2\x80\x8b") { // U+200B Zero-width Word Break
						$breakfound = array($contentctr, $charctr, $cutcontentctr, $cutcharctr, 'discard');
					}
					/////////////////////
					// 5) Break at Hard HYPHEN '-' or U+2010
					/////////////////////
					else if (isset($this->textparam['hyphens']) && $this->textparam['hyphens'] != 2 && ($prevchar == '-' || $prevchar == "\xe2\x80\x90")) {
						// Don't break a URL
						// Look back to get first part of current word
						$checkw = '';
						for ($ac = $charctr - 1; $ac >= 0; $ac--) {
							if ($this->usingCoreFont) {
								$addc = substr($currContent, $ac, 1);
							} else {
								$addc = mb_substr($currContent, $ac, 1, $this->mb_enc);
							}
							if ($addc == ' ') {
								break;
							}
							$checkw = $addc . $checkw;
						}
						// Don't break if HyphenMinus AND (a URL or before a numeral or before a >)
						if ((!preg_match('/(http:|ftp:|https:|www\.)/', $checkw) && $checkchar != '>' && !preg_match('/[0-9]/', $checkchar)) || $prevchar == "\xe2\x80\x90") {
							$breakfound = array($cutcontentctr, $cutcharctr, $cutcontentctr, $cutcharctr, 'cut');
						}
					}
					/////////////////////
					// 6) Break at Soft HYPHEN (replace with hard hyphen)
					/////////////////////
					else if (isset($this->textparam['hyphens']) && $this->textparam['hyphens'] != 2 && !$this->usingCoreFont && $prevchar == "\xc2\xad") {
						$breakfound = array($cutcontentctr, $cutcharctr, $cutcontentctr, $cutcharctr, 'cut');
						$content[$contentctr] = mb_substr($content[$contentctr], 0, $charctr, $this->mb_enc) . '-' . mb_substr($content[$contentctr], $charctr + 1, mb_strlen($content[$contentctr]), $this->mb_enc);
						if (!empty($cOTLdata[$contentctr])) {
							$cOTLdata[$contentctr]['char_data'][$charctr] = array('bidi_class' => 9, 'uni' => 45);
							$cOTLdata[$contentctr]['group'][$charctr] = 'C';
						}
					} else if (isset($this->textparam['hyphens']) && $this->textparam['hyphens'] != 2 && $this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats' && $prevchar == chr(173)) {
						$breakfound = array($cutcontentctr, $cutcharctr, $cutcontentctr, $cutcharctr, 'cut');
						$content[$contentctr] = substr($content[$contentctr], 0, $charctr) . '-' . substr($content[$contentctr], $charctr + 1);
					}
					/* -- CJK-FONTS -- */
					/////////////////////
					// 7) Break at CJK characters (unless forbidden characters to end or start line)
					// CJK Avoiding line break in the middle of numerals
					/////////////////////
					else if (!$this->usingCoreFont && $this->checkCJK && preg_match("/[" . $this->pregCJKchars . "]/u", $checkchar) &&
						!preg_match("/[" . $this->CJKfollowing . "]/u", $checkchar) && !preg_match("/[" . $this->CJKleading . "]/u", $prevchar) &&
						!(preg_match("/[0-9\x{ff10}-\x{ff19}]/u", $prevchar) && preg_match("/[0-9\x{ff10}-\x{ff19}]/u", $checkchar))) {
						$breakfound = array($cutcontentctr, $cutcharctr, $cutcontentctr, $cutcharctr, 'cut');
					}
					/* -- END CJK-FONTS -- */
					/////////////////////
					// 8) Break at OBJECT (Break before all objects here - selected objects are moved forward to next line below e.g. dottab)
					/////////////////////
					if (isset($this->objectbuffer[$contentctr])) {
						$breakfound = array($cutcontentctr, $cutcharctr, $cutcontentctr, $cutcharctr, 'cut');
					}


					$checkchar = $prevchar;
				}

				// If a line-break opportunity found:
				if (is_array($breakfound)) {
					$contentctr = $breakfound[0];
					$charctr = $breakfound[1];
					$cutcontentctr = $breakfound[2];
					$cutcharctr = $breakfound[3];
					$type = $breakfound[4];
					// Cache chunks which are already processed, but now need to be passed on to the new line
					for ($ix = count($content) - 1; $ix > $cutcontentctr; $ix--) {
						// save and crop off any subsequent chunks
						/* -- OTL -- */
						if (!empty($sOTLdata)) {
							$tmpOTL = array_pop($cOTLdata);
							$savedPreOTLdata[] = $tmpOTL;
						}
						/* -- END OTL -- */
						$savedPreContent[] = array_pop($content);
						$savedPreContentB[] = array_pop($contentB);
						$savedPreFont[] = array_pop($font);
					}

					// Next cache the part which will start the next line
					if ($this->usingCoreFont) {
						$savedPreContent[] = substr($content[$cutcontentctr], $cutcharctr);
					} else {
						$savedPreContent[] = mb_substr($content[$cutcontentctr], $cutcharctr, mb_strlen($content[$cutcontentctr]), $this->mb_enc);
					}
					$savedPreContentB[] = preg_replace('/L/', '', $contentB[$cutcontentctr]);
					$savedPreFont[] = $font[$cutcontentctr];
					/* -- OTL -- */
					if (!empty($sOTLdata)) {
						$savedPreOTLdata[] = $this->otl->splitOTLdata($cOTLdata[$cutcontentctr], $cutcharctr, $cutcharctr);
					}
					/* -- END OTL -- */


					// Finally adjust the Current content which ends this line
					if ($cutcharctr == 0 && $type == 'discard') {
						array_pop($content);
						array_pop($contentB);
						array_pop($font);
						array_pop($cOTLdata);
					}

					$currContent = & $content[count($content) - 1];
					if ($this->usingCoreFont) {
						$currContent = substr($currContent, 0, $charctr);
					} else {
						$currContent = mb_substr($currContent, 0, $charctr, $this->mb_enc);
					}

					if (!empty($sOTLdata)) {
						$savedPreOTLdata[] = $this->otl->splitOTLdata($cOTLdata[(count($cOTLdata) - 1)], mb_strlen($currContent, $this->mb_enc));
					}

					if (strpos($contentB[(count($contentB) - 1)], 'R') !== false) {   // ???
						$contentB[count($content) - 1] = preg_replace('/R/', '', $contentB[count($content) - 1]); // ???
					}

					if ($type == 'hyphen') {
						$currContent .= '-';
						if (!empty($cOTLdata[(count($cOTLdata) - 1)])) {
							$cOTLdata[(count($cOTLdata) - 1)]['char_data'][] = array('bidi_class' => 9, 'uni' => 45);
							$cOTLdata[(count($cOTLdata) - 1)]['group'] .= 'C';
						}
					}

					$savedContent = '';
					$savedContentB = '';
					$savedFont = array();
					$savedOTLdata = array();
				}
				// If no line-break opportunity found - split at current position
				// or - Next character ($c) is suitable to add as overhanging or squeezed punctuation, or Oikomi, as set above by:
				// 1) CJK Overflowing a) punctuation or b) Oikomi
				// in which case $breakfound==1 and NOT array

				if (!is_array($breakfound)) {
					$savedFont = $this->saveFont();
					if (!empty($sOTLdata)) {
						$savedOTLdata = $this->otl->splitOTLdata($cOTLdata[(count($cOTLdata) - 1)], mb_strlen($currContent, $this->mb_enc));
					}
				}

				if ($content[count($content) - 1] == '' && !isset($this->objectbuffer[count($content) - 1])) {
					array_pop($content);
					array_pop($contentB);
					array_pop($font);
					array_pop($cOTLdata);
					$currContent = & $content[count($content) - 1];
				}

				// Right Trim current content - including CJK space, and for OTLdata
				// incl. CJK - strip CJK space at end of line &#x3000; = \xe3\x80\x80 = CJK space
				$currContent = rtrim($currContent);
				if ($this->checkCJK) {
					$currContent = preg_replace("/\xe3\x80\x80$/", '', $currContent);
				} // *CJK-FONTS*
				/* -- OTL -- */
				if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
					$this->otl->trimOTLdata($cOTLdata[count($cOTLdata) - 1], false, true); // NB also does U+3000
				}
				/* -- END OTL -- */


				// Selected OBJECTS are moved forward to next line, unless they come before a space or U+200B (type='discard')
				if (isset($this->objectbuffer[(count($content) - 1)]) && (!isset($type) || $type != 'discard')) {
					$objtype = $this->objectbuffer[(count($content) - 1)]['type'];
					if ($objtype == 'dottab' || $objtype == 'bookmark' || $objtype == 'indexentry' || $objtype == 'toc' || $objtype == 'annot') {
						$savedObj = array_pop($this->objectbuffer);
					}
				}


				// Decimal alignment (cancel if wraps to > 1 line)
				if ($is_table && substr($align, 0, 1) == 'D') {
					$align = substr($align, 2, 1);
				}

				$lineBox = array();

				$this->_setInlineBlockHeights($lineBox, $stackHeight, $content, $font, $is_table);

				// update $contentWidth since it has changed with cropping
				$contentWidth = 0;

				$inclCursive = false;
				foreach ($content as $k => $chunk) {
					if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]) {
						// LIST MARKERS
						if ($this->objectbuffer[$k]['type'] == 'image' && isset($this->objectbuffer[$k]['listmarker']) && $this->objectbuffer[$k]['listmarker']) {
							if ($this->objectbuffer[$k]['listmarkerposition'] != 'outside') {
								$contentWidth += $this->objectbuffer[$k]['OUTER-WIDTH'] * _MPDFK;
							}
						} else {
							$contentWidth += $this->objectbuffer[$k]['OUTER-WIDTH'] * _MPDFK;
						}
					} else if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
						$this->restoreFont($font[$k], false);
						if ($this->checkCJK && $k == count($content) - 1 && $CJKoverflow && $align == 'J' && $this->allowCJKoverflow && $this->CJKforceend) {
							// force-end overhang
							$hanger = mb_substr($chunk, mb_strlen($chunk, $this->mb_enc) - 1, 1, $this->mb_enc);
							// Probably ought to do something with char_data and GPOS in cOTLdata...
							$content[$k] = $chunk = mb_substr($chunk, 0, mb_strlen($chunk, $this->mb_enc) - 1, $this->mb_enc);
						}

						// Soft Hyphens chr(173) + Replace NBSP with SPACE + Set inclcursive if includes CURSIVE TEXT
						if (!$this->usingCoreFont) {
							/* -- OTL -- */
							if ((isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) || !empty($sOTLdata)) {
								$this->otl->removeChar($chunk, $cOTLdata[$k], "\xc2\xad");
								$this->otl->replaceSpace($chunk, $cOTLdata[$k]); // NBSP -> space
								if (preg_match("/([" . $this->pregCURSchars . "])/u", $chunk)) {
									$inclCursive = true;
								}
								$content[$k] = $chunk;
							}
							/* -- END OTL -- */ else {  // *OTL*
								$content[$k] = $chunk = str_replace("\xc2\xad", '', $chunk);
								$content[$k] = $chunk = str_replace(chr(194) . chr(160), chr(32), $chunk);
							} // *OTL*
						} else if ($this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats') {
							$content[$k] = $chunk = str_replace(chr(173), '', $chunk);
							$content[$k] = $chunk = str_replace(chr(160), chr(32), $chunk);
						}

						$contentWidth += $this->GetStringWidth($chunk, true, (isset($cOTLdata[$k]) ? $cOTLdata[$k] : false), $this->textvar) * _MPDFK;  // mPDF 5.7.1
						if (!empty($this->spanborddet)) {
							if (isset($this->spanborddet['L']['w']) && strpos($contentB[$k], 'L') !== false)
								$contentWidth += $this->spanborddet['L']['w'] * _MPDFK;
							if (isset($this->spanborddet['R']['w']) && strpos($contentB[$k], 'R') !== false)
								$contentWidth += $this->spanborddet['R']['w'] * _MPDFK;
						}
					}
				}

				$lastfontreqstyle = (isset($font[count($font) - 1]['ReqFontStyle']) ? $font[count($font) - 1]['ReqFontStyle'] : '');
				$lastfontstyle = (isset($font[count($font) - 1]['style']) ? $font[count($font) - 1]['style'] : '');
				if ($blockdir == 'ltr' && strpos($lastfontreqstyle, "I") !== false && strpos($lastfontstyle, "I") === false) { // Artificial italic
					$lastitalic = $this->FontSize * 0.15 * _MPDFK;
				} else {
					$lastitalic = 0;
				}




				// NOW FORMAT THE LINE TO OUTPUT
				if (!$table_draft) {
					// DIRECTIONALITY RTL
					$chunkorder = range(0, count($content) - 1); // mPDF 5.7
					/* -- OTL -- */
					// mPDF 6
					if ($blockdir == 'rtl' || $this->biDirectional) {
						$this->otl->_bidiReorder($chunkorder, $content, $cOTLdata, $blockdir);
						// From this point on, $content and $cOTLdata may contain more elements (and re-ordered) compared to
						// $this->objectbuffer and $font ($chunkorder contains the mapping)
					}

					/* -- END OTL -- */
					// Remove any XAdvance from OTL data at end of line
					foreach ($chunkorder AS $aord => $k) {
						if (count($cOTLdata)) {
							$this->restoreFont($font[$k], false);
							// ...WriteFlowingBlock...
							if ($aord == count($chunkorder) - 1 && isset($cOTLdata[$aord]['group'])) { // Last chunk on line
								$nGPOS = strlen($cOTLdata[$aord]['group']) - 1; // Last character
								if (isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL']) || isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'])) {
									if (isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'])) {
										$w = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'] * 1000 / $this->CurrentFont['unitsPerEm'];
									} else {
										$w = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'] * 1000 / $this->CurrentFont['unitsPerEm'];
									}
									$w *= ($this->FontSize / 1000);
									$contentWidth -= $w * _MPDFK;
									$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'] = 0;
									$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'] = 0;
								}

								// If last character has an XPlacement set, adjust width calculation, and add to XAdvance to account for it
								if (isset($cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'])) {
									$w = -$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'] * 1000 / $this->CurrentFont['unitsPerEm'];
									$w *= ($this->FontSize / 1000);
									$contentWidth -= $w * _MPDFK;
									$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceL'] = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'];
									$cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XAdvanceR'] = $cOTLdata[$aord]['GPOSinfo'][$nGPOS]['XPlacement'];
								}
							}
						}
					}

					// JUSTIFICATION J
					$jcharspacing = 0;
					$jws = 0;
					$nb_carac = 0;
					$nb_spaces = 0;
					$jkashida = 0;
					// if it's justified, we need to find the char/word spacing (or if hanger $this->CJKforceend)
					if (($align == 'J' && !$CJKoverflow) || (($contentWidth + $lastitalic > $maxWidth - $WidthCorrection - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) ) + 0.001) && (!$CJKoverflow || ($CJKoverflow && !$this->allowCJKoverflow))) || $CJKoverflow && $align == 'J' && $this->allowCJKoverflow && $hanger && $this->CJKforceend) {   // 0.001 is to correct for deviations converting mm=>pts
						// JUSTIFY J (Use character spacing)
						// WORD SPACING
						foreach ($chunkorder AS $aord => $k) { // mPDF 5.7
							$chunk = $content[$aord];
							if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
								$nb_carac += mb_strlen($chunk, $this->mb_enc);
								$nb_spaces += mb_substr_count($chunk, ' ', $this->mb_enc);
								// Use GPOS OTL
								if (isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0xFF)) {
									if (isset($cOTLdata[$aord]['group']) && $cOTLdata[$aord]['group']) {
										$nb_carac -= substr_count($cOTLdata[$aord]['group'], 'M');
									}
								}
							} else {
								$nb_carac ++;
							} // mPDF 6 allow spacing for inline object
						}
						// GetJSpacing adds kashida spacing to GPOSinfo if appropriate for Font
						list($jcharspacing, $jws, $jkashida) = $this->GetJspacing($nb_carac, $nb_spaces, ($maxWidth - $lastitalic - $contentWidth - $WidthCorrection - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) )), $inclCursive, $cOTLdata);
					}

					// WORD SPACING
					$empty = $maxWidth - $lastitalic - $WidthCorrection - $contentWidth - (($this->cMarginL + $this->cMarginR) * _MPDFK) - ($paddingL + $paddingR + (($fpaddingL + $fpaddingR) * _MPDFK) );

					$empty -= ($jcharspacing * ($nb_carac - 1)); // mPDF 6 nb_carac MINUS 1
					$empty -= ($jws * $nb_spaces);
					$empty -= ($jkashida);
					$empty /= _MPDFK;

					$b = ''; //do not use borders
					// Get PAGEBREAK TO TEST for height including the top border/padding
					$check_h = max($this->divheight, $stackHeight);
					if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($this->blklvl > 0) && ($lineCount == 1) && (!$is_table)) {
						$check_h += ($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['border_top']['w']);
					}

					if ($this->ColActive && $check_h > ($this->PageBreakTrigger - $this->y0)) {
						$this->SetCol($this->NbCol - 1);
					}

					// PAGEBREAK
					// 'If' below used in order to fix "first-line of other page with justify on" bug
					if (!$is_table && ($this->y + $check_h) > $this->PageBreakTrigger and ! $this->InFooter and $this->AcceptPageBreak()) {
						$bak_x = $this->x; //Current X position
						// WORD SPACING
						$ws = $this->ws; //Word Spacing
						$charspacing = $this->charspacing; //Character Spacing
						$this->ResetSpacing();

						$this->AddPage($this->CurOrientation);

						$this->x = $bak_x;
						// Added to correct for OddEven Margins
						$currentx += $this->MarginCorrection;
						$this->x += $this->MarginCorrection;

						// WORD SPACING
						$this->SetSpacing($charspacing, $ws);
					}

					if ($this->kwt && !$is_table) { // mPDF 5.7+
						$this->printkwtbuffer();
						$this->kwt = false;
					}


					/* -- COLUMNS -- */
					// COLS
					// COLUMN CHANGE
					if ($this->CurrCol != $oldcolumn) {
						$currentx += $this->ChangeColumn * ($this->ColWidth + $this->ColGap);
						$this->x += $this->ChangeColumn * ($this->ColWidth + $this->ColGap);
						$oldcolumn = $this->CurrCol;
					}

					if ($this->ColActive && !$is_table) {
						$this->breakpoints[$this->CurrCol][] = $this->y;
					} // *COLUMNS*
					/* -- END COLUMNS -- */

					// TOP MARGIN
					if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($this->blk[$this->blklvl]['margin_top']) && ($lineCount == 1) && (!$is_table)) {
						$this->DivLn($this->blk[$this->blklvl]['margin_top'], $this->blklvl - 1, true, $this->blk[$this->blklvl]['margin_collapse']);
						if ($this->ColActive) {
							$this->breakpoints[$this->CurrCol][] = $this->y;
						} // *COLUMNS*
					}


					// Update y0 for top of block (used to paint border)
					if (($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 1) && (!$is_table)) {
						$this->blk[$this->blklvl]['y0'] = $this->y;
						$this->blk[$this->blklvl]['startpage'] = $this->page;
						if ($this->blk[$this->blklvl]['float']) {
							$this->blk[$this->blklvl]['float_start_y'] = $this->y;
						}
					}

					// TOP PADDING and BORDER spacing/fill
					if (($newblock) && ($blockstate == 1 || $blockstate == 3) && (($this->blk[$this->blklvl]['padding_top']) || ($this->blk[$this->blklvl]['border_top'])) && ($lineCount == 1) && (!$is_table)) {
						// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
						$this->DivLn($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'], -3, true, false, 1);
						if ($this->ColActive) {
							$this->breakpoints[$this->CurrCol][] = $this->y;
						} // *COLUMNS*
					}

					$arraysize = count($chunkorder);

					$margins = ($this->cMarginL + $this->cMarginR) + ($ipaddingL + $ipaddingR + $fpaddingR + $fpaddingR );

					// PAINT BACKGROUND FOR THIS LINE
					if (!$is_table) {
						$this->DivLn($stackHeight, $this->blklvl, false);
					} // false -> don't advance y

					$this->x = $currentx + $this->cMarginL + $ipaddingL + $fpaddingL;
					if ($align == 'R') {
						$this->x += $empty;
					} else if ($align == 'C') {
						$this->x += ($empty / 2);
					}

					// Paragraph INDENT
					if (isset($this->blk[$this->blklvl]['text_indent']) && ($newblock) && ($blockstate == 1 || $blockstate == 3) && ($lineCount == 1) && (!$is_table) && ($blockdir != 'rtl') && ($align != 'C')) {
						$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'], $this->blk[$this->blklvl]['inner_width'], $this->blk[$this->blklvl]['InlineProperties']['size'], false);  // mPDF 5.7.4
						$this->x += $ti;
					}

					// BIDI magic_reverse moved upwards from here

					foreach ($chunkorder AS $aord => $k) { // mPDF 5.7
						$chunk = $content[$aord];

						if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]) {
							$xadj = $this->x - $this->objectbuffer[$k]['OUTER-X'];
							$this->objectbuffer[$k]['OUTER-X'] += $xadj;
							$this->objectbuffer[$k]['BORDER-X'] += $xadj;
							$this->objectbuffer[$k]['INNER-X'] += $xadj;

							if ($this->objectbuffer[$k]['type'] == 'listmarker') {
								$this->objectbuffer[$k]['lineBox'] = $lineBox[-1]; // Block element details for glyph-origin
							}
							$yadj = $this->y - $this->objectbuffer[$k]['OUTER-Y'];
							if ($this->objectbuffer[$k]['type'] == 'dottab') { // mPDF 6 DOTTAB
								$this->objectbuffer[$k]['lineBox'] = $lineBox[$k]; // element details for glyph-origin
							}
							if ($this->objectbuffer[$k]['type'] != 'dottab') { // mPDF 6 DOTTAB
								$yadj += $lineBox[$k]['top'];
							}
							$this->objectbuffer[$k]['OUTER-Y'] += $yadj;
							$this->objectbuffer[$k]['BORDER-Y'] += $yadj;
							$this->objectbuffer[$k]['INNER-Y'] += $yadj;
						}

						$this->restoreFont($font[$k]);  // mPDF 5.7

						$this->SetSpacing(($this->fixedlSpacing * _MPDFK) + $jcharspacing, ($this->fixedlSpacing + $this->minwSpacing) * _MPDFK + $jws);
						// Now unset these values so they don't influence GetStringwidth below or in fn. Cell
						$this->fixedlSpacing = false;
						$this->minwSpacing = 0;

						$save_vis = $this->visibility;
						if (isset($this->textparam['visibility']) && $this->textparam['visibility'] && $this->textparam['visibility'] != $this->visibility) {
							$this->SetVisibility($this->textparam['visibility']);
						}
						// *********** SPAN BACKGROUND COLOR ***************** //
						if ($this->spanbgcolor) {
							$cor = $this->spanbgcolorarray;
							$this->SetFColor($cor);
							$save_fill = $fill;
							$spanfill = 1;
							$fill = 1;
						}
						if (!empty($this->spanborddet)) {
							if (strpos($contentB[$k], 'L') !== false)
								$this->x += (isset($this->spanborddet['L']['w']) ? $this->spanborddet['L']['w'] : 0);
							if (strpos($contentB[$k], 'L') === false)
								$this->spanborddet['L']['s'] = $this->spanborddet['L']['w'] = 0;
							if (strpos($contentB[$k], 'R') === false)
								$this->spanborddet['R']['s'] = $this->spanborddet['R']['w'] = 0;
						}

						// WORD SPACING
						// StringWidth this time includes any kashida spacing
						$stringWidth = $this->GetStringWidth($chunk, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, true);

						$nch = mb_strlen($chunk, $this->mb_enc);
						// Use GPOS OTL
						if (isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0xFF)) {
							if (isset($cOTLdata[$aord]['group']) && $cOTLdata[$aord]['group']) {
								$nch -= substr_count($cOTLdata[$aord]['group'], 'M');
							}
						}
						$stringWidth += ( $this->charspacing * $nch / _MPDFK );

						$stringWidth += ( $this->ws * mb_substr_count($chunk, ' ', $this->mb_enc) / _MPDFK );

						if (isset($this->objectbuffer[$k])) {
							// LIST MARKERS	// mPDF 6  Lists
							if ($this->objectbuffer[$k]['type'] == 'image' && isset($this->objectbuffer[$k]['listmarker']) && $this->objectbuffer[$k]['listmarker'] && $this->objectbuffer[$k]['listmarkerposition'] == 'outside') {
								$stringWidth = 0;
							} else {
								$stringWidth = $this->objectbuffer[$k]['OUTER-WIDTH'];
							}
						}

						if ($stringWidth == 0) {
							$stringWidth = 0.000001;
						}

						if ($aord == $arraysize - 1) {
							$stringWidth -= ( $this->charspacing / _MPDFK );
							if ($this->checkCJK && $CJKoverflow && $align == 'J' && $this->allowCJKoverflow && $hanger && $this->CJKforceend) {
								// force-end overhang
								$this->Cell($stringWidth, $stackHeight, $chunk, '', 0, '', $fill, $this->HREF, $currentx, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false));
								$this->Cell($this->GetStringWidth($hanger), $stackHeight, $hanger, '', 1, '', $fill, $this->HREF, $currentx, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false));
							} else {
								$this->Cell($stringWidth, $stackHeight, $chunk, '', 1, '', $fill, $this->HREF, $currentx, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false)); //mono-style line or last part (skips line)
							}
						} else
							$this->Cell($stringWidth, $stackHeight, $chunk, '', 0, '', $fill, $this->HREF, 0, 0, 0, 'M', $fill, true, (isset($cOTLdata[$aord]) ? $cOTLdata[$aord] : false), $this->textvar, (isset($lineBox[$k]) ? $lineBox[$k] : false)); //first or middle part

						if (!empty($this->spanborddet)) {
							if (strpos($contentB[$k], 'R') !== false && $aord != $arraysize - 1)
								$this->x += $this->spanborddet['R']['w'];
						}
						// *********** SPAN BACKGROUND COLOR OFF - RESET BLOCK BGCOLOR ***************** //
						if (isset($spanfill) && $spanfill) {
							$fill = $save_fill;
							$spanfill = 0;
							if ($fill) {
								$this->SetFColor($bcor);
							}
						}
						if (isset($this->textparam['visibility']) && $this->textparam['visibility'] && $this->visibility != $save_vis) {
							$this->SetVisibility($save_vis);
						}
					}
				} else if ($table_draft) {
					$this->y += $stackHeight;
				}

				if (!$is_table) {
					$this->maxPosR = max($this->maxPosR, ($this->w - $this->rMargin - $this->blk[$this->blklvl]['outer_right_margin']));
					$this->maxPosL = min($this->maxPosL, ($this->lMargin + $this->blk[$this->blklvl]['outer_left_margin']));
				}

				// move on to the next line, reset variables, tack on saved content and current char

				if (!$table_draft)
					$this->printobjectbuffer($is_table, $blockdir);
				$this->objectbuffer = array();


				/* -- CSS-IMAGE-FLOAT -- */
				// Update values if set to skipline
				if ($this->floatmargins) {
					$this->_advanceFloatMargins();
				}
				/* -- END CSS-IMAGE-FLOAT -- */

				// Reset lineheight
				$stackHeight = $this->divheight;
				$valign = 'M';

				$font = array();
				$content = array();
				$contentB = array();
				$cOTLdata = array(); // mPDF 5.7.1
				$contentWidth = 0;
				if (!empty($savedObj)) {
					$this->objectbuffer[] = $savedObj;
					$font[] = $savedFont;
					$content[] = '';
					$contentB[] = '';
					$cOTLdata[] = array(); // mPDF 5.7.1
					$contentWidth += $savedObj['OUTER-WIDTH'] * _MPDFK;
				}
				if (count($savedPreContent) > 0) {
					for ($ix = count($savedPreContent) - 1; $ix >= 0; $ix--) {
						$font[] = $savedPreFont[$ix];
						$content[] = $savedPreContent[$ix];
						$contentB[] = $savedPreContentB[$ix];
						if (!empty($sOTLdata)) {
							$cOTLdata[] = $savedPreOTLdata[$ix];
						}
						$this->restoreFont($savedPreFont[$ix]);
						$lbw = $rbw = 0; // Border widths
						if (!empty($this->spanborddet)) {
							$lbw = (isset($this->spanborddet['L']['w']) ? $this->spanborddet['L']['w'] : 0);
							$rbw = (isset($this->spanborddet['R']['w']) ? $this->spanborddet['R']['w'] : 0);
						}
						if ($ix > 0) {
							$contentWidth += $this->GetStringWidth($savedPreContent[$ix], true, (isset($savedPreOTLdata[$ix]) ? $savedPreOTLdata[$ix] : false), $this->textvar) * _MPDFK; // mPDF 5.7.1
							if (strpos($savedPreContentB[$ix], 'L') !== false)
								$contentWidth += $lbw;
							if (strpos($savedPreContentB[$ix], 'R') !== false)
								$contentWidth += $rbw;
						}
					}
					$savedPreContent = array();
					$savedPreContentB = array();
					$savedPreOTLdata = array(); // mPDF 5.7.1
					$savedPreFont = array();
					$content[(count($content) - 1)] .= $c;
				}
				else {
					$font[] = $savedFont;
					$content[] = $savedContent . $c;
					$contentB[] = $savedContentB;
					$cOTLdata[] = $savedOTLdata; // mPDF 5.7.1
				}

				$currContent = & $content[(count($content) - 1)];
				$this->restoreFont($font[(count($font) - 1)]); // mPDF 6.0

				/* -- CJK-FONTS -- */
				// CJK - strip CJK space at start of line
				// &#x3000; = \xe3\x80\x80 = CJK space
				if ($this->checkCJK && $currContent == "\xe3\x80\x80") {
					$currContent = '';
					if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
						$this->otl->trimOTLdata($cOTLdata[count($cOTLdata) - 1], true, false); // left trim U+3000
					}
				}
				/* -- END CJK-FONTS -- */

				$lbw = $rbw = 0; // Border widths
				if (!empty($this->spanborddet)) {
					$lbw = (isset($this->spanborddet['L']['w']) ? $this->spanborddet['L']['w'] : 0);
					$rbw = (isset($this->spanborddet['R']['w']) ? $this->spanborddet['R']['w'] : 0);
				}

				$contentWidth += $this->GetStringWidth($currContent, false, (isset($cOTLdata[(count($cOTLdata) - 1)]) ? $cOTLdata[(count($cOTLdata) - 1)] : false), $this->textvar) * _MPDFK; // mPDF 5.7.1
				if (strpos($savedContentB, 'L') !== false)
					$contentWidth += $lbw;
				$CJKoverflow = false;
				$hanger = '';
			}
			// another character will fit, so add it on
			else {
				$contentWidth += $cw;
				$currContent .= $c;
			}
		}

		unset($content);
		unset($contentB);
	}

//----------------------END OF FLOWING BLOCK------------------------------------//


	/* -- CSS-IMAGE-FLOAT -- */
// Update values if set to skipline
	

	/* -- END CSS-IMAGE-FLOAT -- */



	/* -- END HTML-CSS -- */

	function _SetTextRendering($mode)
	{
		if (!(($mode == 0) || ($mode == 1) || ($mode == 2)))
			$this->Error("Text rendering mode should be 0, 1 or 2 (value : $mode)");
		$tr = ($mode . ' Tr');
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']))) {
			$this->_out($tr);
		}
		$this->pageoutput[$this->page]['TextRendering'] = $tr;
	}

	function SetTextOutline($params = array())
	{
		if (isset($params['outline-s']) && $params['outline-s']) {
			$this->SetLineWidth($params['outline-WIDTH']);
			$this->SetDColor($params['outline-COLOR']);
			$tr = ('2 Tr');
			if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']))) {
				$this->_out($tr);
			}
			$this->pageoutput[$this->page]['TextRendering'] = $tr;
		} else { //Now resets all values
			$this->SetLineWidth(0.2);
			$this->SetDColor($this->ConvertColor(0));
			$this->_SetTextRendering(0);
			$tr = ('0 Tr');
			if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']))) {
				$this->_out($tr);
			}
			$this->pageoutput[$this->page]['TextRendering'] = $tr;
		}
	}



	function SetLineJoin($mode = 0)
	{
		$s = sprintf('%d j', $mode);
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['LineJoin']) && $this->pageoutput[$this->page]['LineJoin'] != $s) || !isset($this->pageoutput[$this->page]['LineJoin']))) {
			$this->_out($s);
		}
		$this->pageoutput[$this->page]['LineJoin'] = $s;
	}

	function SetLineCap($mode = 2)
	{
		$s = sprintf('%d J', $mode);
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['LineCap']) && $this->pageoutput[$this->page]['LineCap'] != $s) || !isset($this->pageoutput[$this->page]['LineCap']))) {
			$this->_out($s);
		}
		$this->pageoutput[$this->page]['LineCap'] = $s;
	}

	function SetDash($black = false, $white = false)
	{
		if ($black and $white)
			$s = sprintf('[%.3F %.3F] 0 d', $black * _MPDFK, $white * _MPDFK);
		else
			$s = '[] 0 d';
		if ($this->page > 0 && ((isset($this->pageoutput[$this->page]['Dash']) && $this->pageoutput[$this->page]['Dash'] != $s) || !isset($this->pageoutput[$this->page]['Dash']))) {
			$this->_out($s);
		}
		$this->pageoutput[$this->page]['Dash'] = $s;
	}

	function SetDisplayPreferences($preferences)
	{
		// String containing any or none of /HideMenubar/HideToolbar/HideWindowUI/DisplayDocTitle/CenterWindow/FitWindow
		$this->DisplayPreferences .= $preferences;
	}

	function Ln($h = '', $collapsible = 0)
	{
		// Added collapsible to allow collapsible top-margin on new page
		//Line feed; default value is last cell height
		$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];
		if ($collapsible && ($this->y == $this->tMargin) && (!$this->ColActive)) {
			$h = 0;
		}
		if (is_string($h))
			$this->y+=$this->lasth;
		else
			$this->y+=$h;
	}

	/* -- HTML-CSS -- */

	function DivLn($h, $level = -3, $move_y = true, $collapsible = false, $state = 0)
	{
		// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
		// Used in Columns and keep-with-table i.e. "kwt"
		// writes background block by block so it can be repositioned
		// and also used in writingFlowingBlock at top and bottom of blocks to move y (not to draw/paint anything)
		// adds lines (y) where DIV bgcolors are filled in
		// this->x is returned as it was
		// allows .00001 as nominal height used for bookmarks/annotations etc.
		if ($collapsible && (sprintf("%0.4f", $this->y) == sprintf("%0.4f", $this->tMargin)) && (!$this->ColActive)) {
			return;
		}

		// mPDF 6 Columns
		//   if ($collapsible && (sprintf("%0.4f", $this->y)==sprintf("%0.4f", $this->y0)) && ($this->ColActive) && $this->CurrCol == 0) { return; }	// *COLUMNS*
		if ($collapsible && (sprintf("%0.4f", $this->y) == sprintf("%0.4f", $this->y0)) && ($this->ColActive)) {
			return;
		} // *COLUMNS*
		// Still use this method if columns or keep-with-table, as it allows repositioning later
		// otherwise, now uses PaintDivBB()
		if (!$this->ColActive && !$this->kwt) {
			if ($move_y && !$this->ColActive) {
				$this->y += $h;
			}
			return;
		}

		if ($level == -3) {
			$level = $this->blklvl;
		}
		$firstblockfill = $this->GetFirstBlockFill();
		if ($firstblockfill && $this->blklvl > 0 && $this->blklvl >= $firstblockfill) {
			$last_x = 0;
			$last_w = 0;
			$last_fc = $this->FillColor;
			$bak_x = $this->x;
			$bak_h = $this->divheight;
			$this->divheight = 0; // Temporarily turn off divheight - as Cell() uses it to check for PageBreak
			for ($blvl = $firstblockfill; $blvl <= $level; $blvl++) {
				$this->x = $this->lMargin + $this->blk[$blvl]['outer_left_margin'];
				// mPDF 6
				if ($this->blk[$blvl]['bgcolor']) {
					$this->SetFColor($this->blk[$blvl]['bgcolorarray']);
				}
				if ($last_x != ($this->lMargin + $this->blk[$blvl]['outer_left_margin']) || ($last_w != $this->blk[$blvl]['width']) || $last_fc != $this->FillColor || (isset($this->blk[$blvl]['border_top']['s']) && $this->blk[$blvl]['border_top']['s']) || (isset($this->blk[$blvl]['border_bottom']['s']) && $this->blk[$blvl]['border_bottom']['s']) || (isset($this->blk[$blvl]['border_left']['s']) && $this->blk[$blvl]['border_left']['s']) || (isset($this->blk[$blvl]['border_right']['s']) && $this->blk[$blvl]['border_right']['s'])) {
					$x = $this->x;
					$this->Cell(($this->blk[$blvl]['width']), $h, '', '', 0, '', 1);
					$this->x = $x;
					if (!$this->keep_block_together && !$this->writingHTMLheader && !$this->writingHTMLfooter) {
						// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
						if ($blvl == $this->blklvl) {
							$this->PaintDivLnBorder($state, $blvl, $h);
						} else {
							$this->PaintDivLnBorder(0, $blvl, $h);
						}
					}
				}
				$last_x = $this->lMargin + $this->blk[$blvl]['outer_left_margin'];
				$last_w = $this->blk[$blvl]['width'];
				$last_fc = $this->FillColor;
			}
			// Reset current block fill
			if (isset($this->blk[$this->blklvl]['bgcolorarray'])) {
				$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
				$this->SetFColor($bcor);
			}
			$this->x = $bak_x;
			$this->divheight = $bak_h;
		}
		if ($move_y) {
			$this->y += $h;
		}
	}

	/* -- END HTML-CSS -- */

	function SetX($x)
	{
		//Set x position
		if ($x >= 0)
			$this->x = $x;
		else
			$this->x = $this->w + $x;
	}

	function SetY($y)
	{
		//Set y position and reset x
		$this->x = $this->lMargin;
		if ($y >= 0)
			$this->y = $y;
		else
			$this->y = $this->h + $y;
	}

	function SetXY($x, $y)
	{
		//Set x and y positions
		$this->SetY($y);
		$this->SetX($x);
	}

	function Output($name = '', $dest = '')
	{
		//Output PDF to some destination
		if ($this->showStats) {
			echo '<div>Generated in ' . sprintf('%.2F', (microtime(true) - $this->time0)) . ' seconds</div>';
		}
		//Finish document if necessary
		if ($this->progressBar) {
			$this->UpdateProgressBar(1, '100', 'Finished');
		} // *PROGRESS-BAR*
		if ($this->state < 3)
			$this->Close();
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '100', 'Finished');
		} // *PROGRESS-BAR*
		// fn. error_get_last is only in PHP>=5.2
		if ($this->debug && function_exists('error_get_last') && error_get_last()) {
			$e = error_get_last();
			if (($e['type'] < 2048 && $e['type'] != 8) || (intval($e['type']) & intval(ini_get("error_reporting")))) {
				echo "<p>Error message detected - PDF file generation aborted.</p>";
				echo $e['message'] . '<br />';
				echo 'File: ' . $e['file'] . '<br />';
				echo 'Line: ' . $e['line'] . '<br />';
				exit;
			}
		}


		if (($this->PDFA || $this->PDFX) && $this->encrypted) {
			$this->Error("PDFA1-b or PDFX/1-a does not permit encryption of documents.");
		}
		if (count($this->PDFAXwarnings) && (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto))) {
			if ($this->PDFA) {
				echo '<div>WARNING - This file could not be generated as it stands as a PDFA1-b compliant file.</div>';
				echo '<div>These issues can be automatically fixed by mPDF using <i>$mpdf-&gt;PDFAauto=true;</i></div>';
				echo '<div>Action that mPDF will take to automatically force PDFA1-b compliance are shown in brackets.</div>';
			} else {
				echo '<div>WARNING - This file could not be generated as it stands as a PDFX/1-a compliant file.</div>';
				echo '<div>These issues can be automatically fixed by mPDF using <i>$mpdf-&gt;PDFXauto=true;</i></div>';
				echo '<div>Action that mPDF will take to automatically force PDFX/1-a compliance are shown in brackets.</div>';
			}
			echo '<div>Warning(s) generated:</div><ul>';
			$this->PDFAXwarnings = array_unique($this->PDFAXwarnings);
			foreach ($this->PDFAXwarnings AS $w) {
				echo '<li>' . $w . '</li>';
			}
			echo '</ul>';
			exit;
		}

		if ($this->showStats) {
			echo '<div>Compiled in ' . sprintf('%.2F', (microtime(true) - $this->time0)) . ' seconds (total)</div>';
			echo '<div>Peak Memory usage ' . number_format((memory_get_peak_usage(true) / (1024 * 1024)), 2) . ' MB</div>';
			echo '<div>PDF file size ' . number_format((strlen($this->buffer) / 1024)) . ' kB</div>';
			echo '<div>Number of fonts ' . count($this->fonts) . '</div>';
			exit;
		}


		if (is_bool($dest))
			$dest = $dest ? 'D' : 'F';
		$dest = strtoupper($dest);
		if ($dest == '') {
			if ($name == '') {
				$name = 'mpdf.pdf';
				$dest = 'I';
			} else {
				$dest = 'F';
			}
		}

		/* -- PROGRESS-BAR -- */
		if ($this->progressBar && ($dest == 'D' || $dest == 'I')) {
			if ($name == '') {
				$name = 'mpdf.pdf';
			}
			$tempfile = '_tempPDF' . uniqid(rand(1, 100000), true);
			//Save to local file
			$f = fopen(_MPDF_TEMP_PATH . $tempfile . '.pdf', 'wb');
			if (!$f)
				$this->Error('Unable to create temporary output file: ' . $tempfile . '.pdf');
			fwrite($f, $this->buffer, strlen($this->buffer));
			fclose($f);
			$this->UpdateProgressBar(3, '', 'Finished');

			echo '<script type="text/javascript">

		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", "' . _MPDF_URI . 'includes/out.php");

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "filename");
		hiddenField.setAttribute("value", "' . $tempfile . '");
		form.appendChild(hiddenField);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "dest");
		hiddenField.setAttribute("value", "' . $dest . '");
		form.appendChild(hiddenField);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "opname");
		hiddenField.setAttribute("value", "' . $name . '");
		form.appendChild(hiddenField);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "path");
		hiddenField.setAttribute("value", "' . urlencode(_MPDF_TEMP_PATH) . '");
		form.appendChild(hiddenField);

		document.body.appendChild(form);
		form.submit();

		</script>
		</div>
		</body>
		</html>';
			exit;
		}
		else {
			if ($this->progressBar) {
				$this->UpdateProgressBar(3, '', 'Finished');
			}
			/* -- END PROGRESS-BAR -- */

			switch ($dest) {
				case 'I':
					if ($this->debug && !$this->allow_output_buffering && ob_get_contents()) {
						echo "<p>Output has already been sent from the script - PDF file generation aborted.</p>";
						exit;
					}
					//Send to standard output
					if (PHP_SAPI != 'cli') {
						//We send to a browser
						header('Content-Type: application/pdf');
						if (headers_sent())
							$this->Error('Some data has already been output to browser, can\'t send PDF file');
						if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
							// don't use length if server using compression
							header('Content-Length: ' . strlen($this->buffer));
						}
						header('Content-disposition: inline; filename="' . $name . '"');
						header('Cache-Control: public, must-revalidate, max-age=0');
						header('Pragma: public');
						header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
						header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
					}
					echo $this->buffer;
					break;
				case 'D':
					//Download file
					header('Content-Description: File Transfer');
					if (headers_sent())
						$this->Error('Some data has already been output to browser, can\'t send PDF file');
					header('Content-Transfer-Encoding: binary');
					header('Cache-Control: public, must-revalidate, max-age=0');
					header('Pragma: public');
					header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
					header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
					header('Content-Type: application/force-download');
					header('Content-Type: application/octet-stream', false);
					header('Content-Type: application/download', false);
					header('Content-Type: application/pdf', false);
					if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
						// don't use length if server using compression
						header('Content-Length: ' . strlen($this->buffer));
					}
					header('Content-disposition: attachment; filename="' . $name . '"');
					echo $this->buffer;
					break;
				case 'F':
					//Save to local file
					$f = fopen($name, 'wb');
					if (!$f)
						$this->Error('Unable to create output file: ' . $name);
					fwrite($f, $this->buffer, strlen($this->buffer));
					fclose($f);
					break;
				case 'S':
					//Return as a string
					return $this->buffer;
				default:
					$this->Error('Incorrect output destination: ' . $dest);
			}
		} // *PROGRESS-BAR*
		//======================================================================================================
		// DELETE OLD TMP FILES - Housekeeping
		// Delete any files in tmp/ directory that are >1 hrs old
		$interval = 3600;
		if ($handle = @opendir(preg_replace('/\/$/', '', _MPDF_TEMP_PATH))) { // mPDF 5.7.3
			while (false !== ($file = readdir($handle))) {
				if (($file != "..") && ($file != ".") && !is_dir($file) && ((filemtime(_MPDF_TEMP_PATH . $file) + $interval) < time()) && (substr($file, 0, 1) !== '.') && ($file != 'dummy.txt')) { // mPDF 5.7.3
					unlink(_MPDF_TEMP_PATH . $file);
				}
			}
			closedir($handle);
		}
		//==============================================================================================================

		return '';
	}

// *****************************************************************************
//                                                                             *
//                             Protected methods                               *
//                                                                             *
// *****************************************************************************
	function _dochecks()
	{
		//Check for locale-related bug
		if (1.1 == 1)
			$this->Error('Don\'t alter the locale before including mPDF');
		//Check for decimal separator
		if (sprintf('%.1f', 1.0) != '1.0')
			setlocale(LC_NUMERIC, 'C');
		$mqr = ini_get("magic_quotes_runtime");
		if ($mqr) {
			$this->Error('mPDF requires magic_quotes_runtime to be turned off e.g. by using ini_set("magic_quotes_runtime", 0);');
		}
	}

	function _puthtmlheaders()
	{
		$this->state = 2;
		$nb = $this->page;
		for ($n = 1; $n <= $nb; $n++) {
			if ($this->mirrorMargins && $n % 2 == 0) {
				$OE = 'E';
			} // EVEN
			else {
				$OE = 'O';
			}
			$this->page = $n;
			$pn = $this->docPageNum($n);
			if ($pn)
				$pnstr = $this->pagenumPrefix . $pn . $this->pagenumSuffix;
			else {
				$pnstr = '';
			}
			$pnt = $this->docPageNumTotal($n);
			if ($pnt)
				$pntstr = $this->nbpgPrefix . $pnt . $this->nbpgSuffix;
			else {
				$pntstr = '';
			}
			if (isset($this->saveHTMLHeader[$n][$OE])) {
				$html = $this->saveHTMLHeader[$n][$OE]['html'];
				$this->lMargin = $this->saveHTMLHeader[$n][$OE]['ml'];
				$this->rMargin = $this->saveHTMLHeader[$n][$OE]['mr'];
				$this->tMargin = $this->saveHTMLHeader[$n][$OE]['mh'];
				$this->bMargin = $this->saveHTMLHeader[$n][$OE]['mf'];
				$this->margin_header = $this->saveHTMLHeader[$n][$OE]['mh'];
				$this->margin_footer = $this->saveHTMLHeader[$n][$OE]['mf'];
				$this->w = $this->saveHTMLHeader[$n][$OE]['pw'];
				$this->h = $this->saveHTMLHeader[$n][$OE]['ph'];
				$rotate = (isset($this->saveHTMLHeader[$n][$OE]['rotate']) ? $this->saveHTMLHeader[$n][$OE]['rotate'] : null);
				$this->Reset();
				$this->pageoutput[$n] = array();
				$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
				$this->x = $this->lMargin;
				$this->y = $this->margin_header;
				$html = str_replace('{PAGENO}', $pnstr, $html);
				$html = str_replace($this->aliasNbPgGp, $pntstr, $html); // {nbpg}
				$html = str_replace($this->aliasNbPg, $nb, $html); // {nb}
				$html = preg_replace_callback('/\{DATE\s+(.*?)\}/', array($this, 'date_callback'), $html); // mPDF 5.7

				$this->HTMLheaderPageLinks = array();
				$this->HTMLheaderPageAnnots = array();
				$this->HTMLheaderPageForms = array();
				$this->pageBackgrounds = array();

				$this->writingHTMLheader = true;
				$this->WriteHTML($html, 4); // parameter 4 saves output to $this->headerbuffer
				$this->writingHTMLheader = false;
				$this->Reset();
				$this->pageoutput[$n] = array();

				$s = $this->PrintPageBackgrounds();
				$this->headerbuffer = $s . $this->headerbuffer;
				$os = '';
				if ($rotate) {
					$os .= sprintf('q 0 -1 1 0 0 %.3F cm ', ($this->w * _MPDFK));
					// To rotate the other way i.e. Header to left of page:
					//$os .= sprintf('q 0 1 -1 0 %.3F %.3F cm ',($this->h*_MPDFK), (($this->rMargin - $this->lMargin )*_MPDFK));
				}
				$os .= $this->headerbuffer;
				if ($rotate) {
					$os .= ' Q' . "\n";
				}

				// Writes over the page background but behind any other output on page
				$os = preg_replace('/\\\\/', '\\\\\\\\', $os);
				$this->pages[$n] = preg_replace('/(___HEADER___MARKER' . $this->uniqstr . ')/', "\n" . $os . "\n" . '\\1', $this->pages[$n]);

				$lks = $this->HTMLheaderPageLinks;
				foreach ($lks AS $lk) {
					if ($rotate) {
						$lw = $lk[2];
						$lh = $lk[3];
						$lk[2] = $lh;
						$lk[3] = $lw; // swap width and height
						$ax = $lk[0] / _MPDFK;
						$ay = $lk[1] / _MPDFK;
						$bx = $ay - ($lh / _MPDFK);
						$by = $this->w - $ax;
						$lk[0] = $bx * _MPDFK;
						$lk[1] = ($this->h - $by) * _MPDFK - $lw;
					}
					$this->PageLinks[$n][] = $lk;
				}
				/* -- FORMS -- */
				foreach ($this->HTMLheaderPageForms AS $f) {
					$this->mpdfform->forms[$f['n']] = $f;
				}
				/* -- END FORMS -- */
			}
			if (isset($this->saveHTMLFooter[$n][$OE])) {
				$html = $this->saveHTMLFooter[$this->page][$OE]['html'];
				$this->lMargin = $this->saveHTMLFooter[$n][$OE]['ml'];
				$this->rMargin = $this->saveHTMLFooter[$n][$OE]['mr'];
				$this->tMargin = $this->saveHTMLFooter[$n][$OE]['mh'];
				$this->bMargin = $this->saveHTMLFooter[$n][$OE]['mf'];
				$this->margin_header = $this->saveHTMLFooter[$n][$OE]['mh'];
				$this->margin_footer = $this->saveHTMLFooter[$n][$OE]['mf'];
				$this->w = $this->saveHTMLFooter[$n][$OE]['pw'];
				$this->h = $this->saveHTMLFooter[$n][$OE]['ph'];
				$rotate = (isset($this->saveHTMLFooter[$n][$OE]['rotate']) ? $this->saveHTMLFooter[$n][$OE]['rotate'] : null);
				$this->Reset();
				$this->pageoutput[$n] = array();
				$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
				$this->x = $this->lMargin;
				$top_y = $this->y = $this->h - $this->margin_footer;

				// if bottom-margin==0, corrects to avoid division by zero
				if ($this->y == $this->h) {
					$top_y = $this->y = ($this->h - 0.1);
				}
				$html = str_replace('{PAGENO}', $pnstr, $html);
				$html = str_replace($this->aliasNbPgGp, $pntstr, $html); // {nbpg}
				$html = str_replace($this->aliasNbPg, $nb, $html); // {nb}
				$html = preg_replace_callback('/\{DATE\s+(.*?)\}/', array($this, 'date_callback'), $html); // mPDF 5.7


				$this->HTMLheaderPageLinks = array();
				$this->HTMLheaderPageAnnots = array();
				$this->HTMLheaderPageForms = array();
				$this->pageBackgrounds = array();

				$this->writingHTMLfooter = true;
				$this->InFooter = true;
				$this->WriteHTML($html, 4); // parameter 4 saves output to $this->headerbuffer
				$this->InFooter = false;
				$this->Reset();
				$this->pageoutput[$n] = array();

				$fheight = $this->y - $top_y;
				$adj = -$fheight;

				$s = $this->PrintPageBackgrounds(-$adj);
				$this->headerbuffer = $s . $this->headerbuffer;
				$this->writingHTMLfooter = false; // mPDF 5.7.3  (moved after PrintPageBackgrounds so can adjust position of images in footer)

				$os = '';
				$os .= $this->StartTransform(true) . "\n";
				if ($rotate) {
					$os .= sprintf('q 0 -1 1 0 0 %.3F cm ', ($this->w * _MPDFK));
					// To rotate the other way i.e. Header to left of page:
					//$os .= sprintf('q 0 1 -1 0 %.3F %.3F cm ',($this->h*_MPDFK), (($this->rMargin - $this->lMargin )*_MPDFK));
				}
				$os .= $this->transformTranslate(0, $adj, true) . "\n";
				$os .= $this->headerbuffer;
				if ($rotate) {
					$os .= ' Q' . "\n";
				}
				$os .= $this->StopTransform(true) . "\n";
				// Writes over the page background but behind any other output on page
				$os = preg_replace('/\\\\/', '\\\\\\\\', $os);
				$this->pages[$n] = preg_replace('/(___HEADER___MARKER' . $this->uniqstr . ')/', "\n" . $os . "\n" . '\\1', $this->pages[$n]);

				$lks = $this->HTMLheaderPageLinks;
				foreach ($lks AS $lk) {
					$lk[1] -= $adj * _MPDFK;
					if ($rotate) {
						$lw = $lk[2];
						$lh = $lk[3];
						$lk[2] = $lh;
						$lk[3] = $lw; // swap width and height

						$ax = $lk[0] / _MPDFK;
						$ay = $lk[1] / _MPDFK;
						$bx = $ay - ($lh / _MPDFK);
						$by = $this->w - $ax;
						$lk[0] = $bx * _MPDFK;
						$lk[1] = ($this->h - $by) * _MPDFK - $lw;
					}
					$this->PageLinks[$n][] = $lk;
				}
				/* -- FORMS -- */
				foreach ($this->HTMLheaderPageForms AS $f) {
					$f['y'] += $adj;
					$this->mpdfform->forms[$f['n']] = $f;
				}
				/* -- END FORMS -- */
			}
		}
		$this->page = $nb;
		$this->state = 1;
	}

	function _putpages()
	{
		$nb = $this->page;
		$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';

		if ($this->DefOrientation == 'P') {
			$defwPt = $this->fwPt;
			$defhPt = $this->fhPt;
		} else {
			$defwPt = $this->fhPt;
			$defhPt = $this->fwPt;
		}
		$annotid = (3 + 2 * $nb);

		// Active Forms
		$totaladdnum = 0;
		for ($n = 1; $n <= $nb; $n++) {
			if (isset($this->PageLinks[$n])) {
				$totaladdnum += count($this->PageLinks[$n]);
			}
			/* -- ANNOTATIONS -- */
			if (isset($this->PageAnnots[$n])) {
				foreach ($this->PageAnnots[$n] as $k => $pl) {
					if (!empty($pl['opt']['popup']) || !empty($pl['opt']['file'])) {
						$totaladdnum += 2;
					} else {
						$totaladdnum++;
					}
				}
			}
			/* -- END ANNOTATIONS -- */

			/* -- FORMS -- */
			if (count($this->mpdfform->forms) > 0) {
				$this->mpdfform->countPageForms($n, $totaladdnum);
			}
			/* -- END FORMS -- */
		}
		/* -- FORMS -- */
		// Make a note in the radio button group of the obj_id it will have
		$ctr = 0;
		if (count($this->mpdfform->form_radio_groups)) {
			foreach ($this->mpdfform->form_radio_groups AS $name => $frg) {
				$this->mpdfform->form_radio_groups[$name]['obj_id'] = $annotid + $totaladdnum + $ctr;
				$ctr++;
			}
		}
		/* -- END FORMS -- */

		// Select unused fonts (usually default font)
		$unused = array();
		foreach ($this->fonts as $fk => $font) {
			if (isset($font['type']) && $font['type'] == 'TTF' && !$font['used']) {
				$unused[] = $fk;
			}
		}


		for ($n = 1; $n <= $nb; $n++) {
			$thispage = $this->pages[$n];
			if (isset($this->OrientationChanges[$n])) {
				$hPt = $this->pageDim[$n]['w'] * _MPDFK;
				$wPt = $this->pageDim[$n]['h'] * _MPDFK;
				$owidthPt_LR = $this->pageDim[$n]['outer_width_TB'] * _MPDFK;
				$owidthPt_TB = $this->pageDim[$n]['outer_width_LR'] * _MPDFK;
			} else {
				$wPt = $this->pageDim[$n]['w'] * _MPDFK;
				$hPt = $this->pageDim[$n]['h'] * _MPDFK;
				$owidthPt_LR = $this->pageDim[$n]['outer_width_LR'] * _MPDFK;
				$owidthPt_TB = $this->pageDim[$n]['outer_width_TB'] * _MPDFK;
			}
			// Remove references to unused fonts (usually default font)
			foreach ($unused as $fk) {
				if ($this->fonts[$fk]['sip'] || $this->fonts[$fk]['smp']) {
					foreach ($this->fonts[$fk]['subsetfontids'] AS $k => $fid) {
						$thispage = preg_replace('/\s\/F' . $fid . ' \d[\d.]* Tf\s/is', ' ', $thispage);
					}
				} else {
					$thispage = preg_replace('/\s\/F' . $this->fonts[$fk]['i'] . ' \d[\d.]* Tf\s/is', ' ', $thispage);
				}
			}
			// Clean up repeated /GS1 gs statements
			// For some reason using + for repetition instead of {2,20} crashes PHP Script Interpreter ???
			$thispage = preg_replace('/(\/GS1 gs\n){2,20}/', "/GS1 gs\n", $thispage);

			$thispage = preg_replace('/(\s*___BACKGROUND___PATTERNS' . $this->uniqstr . '\s*)/', " ", $thispage);
			$thispage = preg_replace('/(\s*___HEADER___MARKER' . $this->uniqstr . '\s*)/', " ", $thispage);
			$thispage = preg_replace('/(\s*___PAGE___START' . $this->uniqstr . '\s*)/', " ", $thispage);
			$thispage = preg_replace('/(\s*___TABLE___BACKGROUNDS' . $this->uniqstr . '\s*)/', " ", $thispage);
			// mPDF 5.7.3 TRANSFORMS
			while (preg_match('/(\% BTR(.*?)\% ETR)/is', $thispage, $m)) {
				$thispage = preg_replace('/(\% BTR.*?\% ETR)/is', '', $thispage, 1) . "\n" . $m[2];
			}

			//Page
			$this->_newobj();
			$this->_out('<</Type /Page');
			$this->_out('/Parent 1 0 R');
			if (isset($this->OrientationChanges[$n])) {
				$this->_out(sprintf('/MediaBox [0 0 %.3F %.3F]', $hPt, $wPt));
				//If BleedBox is defined, it must be larger than the TrimBox, but smaller than the MediaBox
				$bleedMargin = $this->pageDim[$n]['bleedMargin'] * _MPDFK;
				if ($bleedMargin && ($owidthPt_TB || $owidthPt_LR)) {
					$x0 = $owidthPt_TB - $bleedMargin;
					$y0 = $owidthPt_LR - $bleedMargin;
					$x1 = $hPt - $owidthPt_TB + $bleedMargin;
					$y1 = $wPt - $owidthPt_LR + $bleedMargin;
					$this->_out(sprintf('/BleedBox [%.3F %.3F %.3F %.3F]', $x0, $y0, $x1, $y1));
				}
				$this->_out(sprintf('/TrimBox [%.3F %.3F %.3F %.3F]', $owidthPt_TB, $owidthPt_LR, ($hPt - $owidthPt_TB), ($wPt - $owidthPt_LR)));
				if (isset($this->OrientationChanges[$n]) && $this->displayDefaultOrientation) {
					if ($this->DefOrientation == 'P') {
						$this->_out('/Rotate 270');
					} else {
						$this->_out('/Rotate 90');
					}
				}
			}
			//else if($wPt != $defwPt || $hPt != $defhPt) {
			else {
				$this->_out(sprintf('/MediaBox [0 0 %.3F %.3F]', $wPt, $hPt));
				$bleedMargin = $this->pageDim[$n]['bleedMargin'] * _MPDFK;
				if ($bleedMargin && ($owidthPt_TB || $owidthPt_LR)) {
					$x0 = $owidthPt_LR - $bleedMargin;
					$y0 = $owidthPt_TB - $bleedMargin;
					$x1 = $wPt - $owidthPt_LR + $bleedMargin;
					$y1 = $hPt - $owidthPt_TB + $bleedMargin;
					$this->_out(sprintf('/BleedBox [%.3F %.3F %.3F %.3F]', $x0, $y0, $x1, $y1));
				}
				$this->_out(sprintf('/TrimBox [%.3F %.3F %.3F %.3F]', $owidthPt_LR, $owidthPt_TB, ($wPt - $owidthPt_LR), ($hPt - $owidthPt_TB)));
			}
			$this->_out('/Resources 2 0 R');

			// Important to keep in RGB colorSpace when using transparency
			if (!$this->PDFA && !$this->PDFX) {
				if ($this->restrictColorSpace == 3)
					$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceCMYK >> ');
				else if ($this->restrictColorSpace == 1)
					$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceGray >> ');
				else
					$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceRGB >> ');
			}

			$annotsnum = 0;
			$embeddedfiles = array(); // mPDF 5.7.2 /EmbeddedFiles

			if (isset($this->PageLinks[$n])) {
				$annotsnum += count($this->PageLinks[$n]);
			}
			/* -- ANNOTATIONS -- */
			if (isset($this->PageAnnots[$n])) {
				foreach ($this->PageAnnots[$n] as $k => $pl) {
					if (!empty($pl['opt']['file'])) {
						$embeddedfiles[$annotsnum + 1] = true;
					} // mPDF 5.7.2 /EmbeddedFiles
					if (!empty($pl['opt']['popup']) || !empty($pl['opt']['file'])) {
						$annotsnum += 2;
					} else {
						$annotsnum++;
					}
					$this->PageAnnots[$n][$k]['pageobj'] = $this->n;
				}
			}
			/* -- END ANNOTATIONS -- */

			/* -- FORMS -- */
			// Active Forms
			$formsnum = 0;
			if (count($this->mpdfform->forms) > 0) {
				foreach ($this->mpdfform->forms as $val) {
					if ($val['page'] == $n)
						$formsnum++;
				}
			}
			/* -- END FORMS -- */
			if ($annotsnum || $formsnum) {
				$s = '/Annots [ ';
				for ($i = 0; $i < $annotsnum; $i++) {
					if (!isset($embeddedfiles[$i])) {
						$s .= ($annotid + $i) . ' 0 R ';
					} // mPDF 5.7.2 /EmbeddedFiles
				}
				$annotid += $annotsnum;
				/* -- FORMS -- */
				if (count($this->mpdfform->forms) > 0) {
					$this->mpdfform->addFormIds($n, $s, $annotid);
				}
				/* -- END FORMS -- */
				$s .= '] ';
				$this->_out($s);
			}

			$this->_out('/Contents ' . ($this->n + 1) . ' 0 R>>');
			$this->_out('endobj');

			//Page content
			$this->_newobj();
			$p = ($this->compress) ? gzcompress($thispage) : $thispage;
			$this->_out('<<' . $filter . '/Length ' . strlen($p) . '>>');
			$this->_putstream($p);
			$this->_out('endobj');
		}
		$this->_putannots(); // mPDF 5.7.2
		//Pages root
		$this->offsets[1] = strlen($this->buffer);
		$this->_out('1 0 obj');
		$this->_out('<</Type /Pages');
		$kids = '/Kids [';
		for ($i = 0; $i < $nb; $i++)
			$kids.=(3 + 2 * $i) . ' 0 R ';
		$this->_out($kids . ']');
		$this->_out('/Count ' . $nb);
		$this->_out(sprintf('/MediaBox [0 0 %.3F %.3F]', $defwPt, $defhPt));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putannots()
	{ // mPDF 5.7.2
		$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
		$nb = $this->page;
		for ($n = 1; $n <= $nb; $n++) {
			$annotobjs = array();
			if (isset($this->PageLinks[$n]) || isset($this->PageAnnots[$n]) || count($this->mpdfform->forms) > 0) {
				$wPt = $this->pageDim[$n]['w'] * _MPDFK;
				$hPt = $this->pageDim[$n]['h'] * _MPDFK;

				//Links
				if (isset($this->PageLinks[$n])) {
					foreach ($this->PageLinks[$n] as $key => $pl) {
						$this->_newobj();
						$annot = '';
						$rect = sprintf('%.3F %.3F %.3F %.3F', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3]);
						$annot .= '<</Type /Annot /Subtype /Link /Rect [' . $rect . ']';
						$annot .= ' /Contents ' . $this->_UTF16BEtextstring($pl[4]);
						$annot .= ' /NM ' . $this->_textstring(sprintf('%04u-%04u', $n, $key));
						$annot .= ' /M ' . $this->_textstring('D:' . date('YmdHis'));
						$annot .= ' /Border [0 0 0]';
						// Use this (instead of /Border) to specify border around link
						//		$annot .= ' /BS <</W 1';	// Width on points; 0 = no line
						//		$annot .= ' /S /D';		// style - [S]olid, [D]ashed, [B]eveled, [I]nset, [U]nderline
						//		$annot .= ' /D [3 2]';		// Dash array - if dashed
						//		$annot .= ' >>';
						//		$annot .= ' /C [1 0 0]';	// Color RGB

						if ($this->PDFA || $this->PDFX) {
							$annot .= ' /F 28';
						}
						if (strpos($pl[4], '@') === 0) {
							$p = substr($pl[4], 1);
							//	$h=isset($this->OrientationChanges[$p]) ? $wPt : $hPt;
							$htarg = $this->pageDim[$p]['h'] * _MPDFK;
							$annot.=sprintf(' /Dest [%d 0 R /XYZ 0 %.3F null]>>', 1 + 2 * $p, $htarg);
						} else if (is_string($pl[4])) {
							$annot .= ' /A <</S /URI /URI ' . $this->_textstring($pl[4]) . '>> >>';
						} else {
							$l = $this->links[$pl[4]];
							// may not be set if #link points to non-existent target
							if (isset($this->pageDim[$l[0]]['h'])) {
								$htarg = $this->pageDim[$l[0]]['h'] * _MPDFK;
							} else {
								$htarg = $this->h * _MPDFK;
							} // doesn't really matter
							$annot.=sprintf(' /Dest [%d 0 R /XYZ 0 %.3F null]>>', 1 + 2 * $l[0], $htarg - $l[1] * _MPDFK);
						}
						$this->_out($annot);
						$this->_out('endobj');
					}
				}


				/* -- ANNOTATIONS -- */
				if (isset($this->PageAnnots[$n])) {
					foreach ($this->PageAnnots[$n] as $key => $pl) {
						if ($pl['opt']['file']) {
							$FileAttachment = true;
						} else {
							$FileAttachment = false;
						}
						$this->_newobj();
						$annot = '';
						$pl['opt'] = array_change_key_case($pl['opt'], CASE_LOWER);
						$x = $pl['x'];
						if ($this->annotMargin <> 0 || $x == 0 || $x < 0) { // Odd page
							$x = ($wPt / _MPDFK) - $this->annotMargin;
						}
						$w = $h = 0;
						$a = $x * _MPDFK;
						$b = $hPt - ($pl['y'] * _MPDFK);
						$annot .= '<</Type /Annot ';
						if ($FileAttachment) {
							$annot .= '/Subtype /FileAttachment ';
							// Need to set a size for FileAttachment icons
							if ($pl['opt']['icon'] == 'Paperclip') {
								$w = 8.235;
								$h = 20;
							} // 7,17
							else if ($pl['opt']['icon'] == 'Tag') {
								$w = 20;
								$h = 16;
							} else if ($pl['opt']['icon'] == 'Graph') {
								$w = 20;
								$h = 20;
							} else {
								$w = 14;
								$h = 20;
							}  // PushPin
							$f = $pl['opt']['file'];
							$f = preg_replace('/^.*\//', '', $f);
							$f = preg_replace('/[^a-zA-Z0-9._]/', '', $f);
							$annot .= '/FS <</Type /Filespec /F (' . $f . ')';
							$annot .= '/EF <</F ' . ($this->n + 1) . ' 0 R>>';
							$annot .= '>>';
						} else {
							$annot .= '/Subtype /Text';
							$w = 20;
							$h = 20;  // mPDF 6
						}
						$rect = sprintf('%.3F %.3F %.3F %.3F', $a, $b - $h, $a + $w, $b);
						$annot .= ' /Rect [' . $rect . ']';

						// contents = description of file in free text
						$annot .= ' /Contents ' . $this->_UTF16BEtextstring($pl['txt']);

						$annot .= ' /NM ' . $this->_textstring(sprintf('%04u-%04u', $n, (2000 + $key)));
						$annot .= ' /M ' . $this->_textstring('D:' . date('YmdHis'));
						$annot .= ' /CreationDate ' . $this->_textstring('D:' . date('YmdHis'));
						$annot .= ' /Border [0 0 0]';
						if ($this->PDFA || $this->PDFX) {
							$annot .= ' /F 28';
							$annot .= ' /CA 1';
						} else if ($pl['opt']['ca'] > 0) {
							$annot .= ' /CA ' . $pl['opt']['ca'];
						}

						$annotcolor = ' /C [';
						if (isset($pl['opt']['c']) AND $pl['opt']['c']) {
							$col = $pl['opt']['c'];
							if ($col{0} == 3 || $col{0} == 5) {
								$annotcolor .= sprintf("%.3F %.3F %.3F", ord($col{1}) / 255, ord($col{2}) / 255, ord($col{3}) / 255);
							} else if ($col{0} == 1) {
								$annotcolor .= sprintf("%.3F", ord($col{1}) / 255);
							} else if ($col{0} == 4 || $col{0} == 6) {
								$annotcolor .= sprintf("%.3F %.3F %.3F %.3F", ord($col{1}) / 100, ord($col{2}) / 100, ord($col{3}) / 100, ord($col{4}) / 100);
							} else {
								$annotcolor .= '1 1 0';
							}
						} else {
							$annotcolor .= '1 1 0';
						}
						$annotcolor .= ']';
						$annot .= $annotcolor;
						// Usually Author
						// Use as Title for fileattachment
						if (isset($pl['opt']['t']) AND is_string($pl['opt']['t'])) {
							$annot .= ' /T ' . $this->_UTF16BEtextstring($pl['opt']['t']);
						}
						if ($FileAttachment) {
							$iconsapp = array('Paperclip', 'Graph', 'PushPin', 'Tag');
						} else {
							$iconsapp = array('Comment', 'Help', 'Insert', 'Key', 'NewParagraph', 'Note', 'Paragraph');
						}
						if (isset($pl['opt']['icon']) AND in_array($pl['opt']['icon'], $iconsapp)) {
							$annot .= ' /Name /' . $pl['opt']['icon'];
						} else if ($FileAttachment) {
							$annot .= ' /Name /PushPin';
						} else {
							$annot .= ' /Name /Note';
						}
						if (!$FileAttachment) {
							// /Subj is PDF 1.5 spec.
							if (isset($pl['opt']['subj']) && !$this->PDFA && !$this->PDFX) {
								$annot .= ' /Subj ' . $this->_UTF16BEtextstring($pl['opt']['subj']);
							}
							if (!empty($pl['opt']['popup'])) {
								$annot .= ' /Open true';
								$annot .= ' /Popup ' . ($this->n + 1) . ' 0 R';
							} else {
								$annot .= ' /Open false';
							}
						}
						$annot .= ' /P ' . $pl['pageobj'] . ' 0 R';
						$annot .= '>>';
						$this->_out($annot);
						$this->_out('endobj');

						if ($FileAttachment) {
							$file = @file_get_contents($pl['opt']['file']) or $this->Error('mPDF Error: Cannot access file attachment - ' . $pl['opt']['file']);
							$filestream = gzcompress($file);
							$this->_newobj();
							$this->_out('<</Type /EmbeddedFile');
							$this->_out('/Length ' . strlen($filestream));
							$this->_out('/Filter /FlateDecode');
							$this->_out('>>');
							$this->_putstream($filestream);
							$this->_out('endobj');
						} else if (!empty($pl['opt']['popup'])) {
							$this->_newobj();
							$annot = '';
							if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][0])) {
								$x = $pl['opt']['popup'][0] * _MPDFK;
							} else {
								$x = $pl['x'] * _MPDFK;
							}
							if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][1])) {
								$y = $hPt - ($pl['opt']['popup'][1] * _MPDFK);
							} else {
								$y = $hPt - ($pl['y'] * _MPDFK);
							}
							if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][2])) {
								$w = $pl['opt']['popup'][2] * _MPDFK;
							} else {
								$w = 180;
							}
							if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][3])) {
								$h = $pl['opt']['popup'][3] * _MPDFK;
							} else {
								$h = 120;
							}
							$rect = sprintf('%.3F %.3F %.3F %.3F', $x, $y - $h, $x + $w, $y);
							$annot .= '<</Type /Annot /Subtype /Popup /Rect [' . $rect . ']';
							$annot .= ' /M ' . $this->_textstring('D:' . date('YmdHis'));
							if ($this->PDFA || $this->PDFX) {
								$annot .= ' /F 28';
							}
							$annot .= ' /Parent ' . ($this->n - 1) . ' 0 R';
							$annot .= '>>';
							$this->_out($annot);
							$this->_out('endobj');
						}
					}
				}
				/* -- END ANNOTATIONS -- */

				/* -- FORMS -- */
				// Active Forms
				if (count($this->mpdfform->forms) > 0) {
					$this->mpdfform->_putFormItems($n, $hPt);
				}
				/* -- END FORMS -- */
			}
		}
		/* -- FORMS -- */
		// Active Forms - Radio Button Group entries
		// Output Radio Button Group form entries (radio_on_obj_id already determined)
		if (count($this->mpdfform->form_radio_groups)) {
			$this->mpdfform->_putRadioItems($n);
		}
		/* -- END FORMS -- */
	}

	/* -- ANNOTATIONS -- */



	/* -- END ANNOTATIONS -- */

	function _putfonts()
	{
		$nf = $this->n;
		foreach ($this->FontFiles as $fontkey => $info) {
			// TrueType embedded
			if (isset($info['type']) && $info['type'] == 'TTF' && !$info['sip'] && !$info['smp']) {
				$used = true;
				$asSubset = false;
				foreach ($this->fonts AS $k => $f) {
					if (isset($f['fontkey']) && $f['fontkey'] == $fontkey && $f['type'] == 'TTF') {
						$used = $f['used'];
						if ($used) {
							$nChars = (ord($f['cw'][0]) << 8) + ord($f['cw'][1]);
							$usage = intval(count($f['subset']) * 100 / $nChars);
							$fsize = $info['length1'];
							// Always subset the very large TTF files
							if ($fsize > ($this->maxTTFFilesize * 1024)) {
								$asSubset = true;
							} else if ($usage < $this->percentSubset) {
								$asSubset = true;
							}
						}
						if ($this->PDFA || $this->PDFX)
							$asSubset = false;
						$this->fonts[$k]['asSubset'] = $asSubset;
						break;
					}
				}
				if ($used && !$asSubset) {
					//Font file embedding
					$this->_newobj();
					$this->FontFiles[$fontkey]['n'] = $this->n;
					$font = '';
					$originalsize = $info['length1'];
					if ($this->repackageTTF || $this->fonts[$fontkey]['TTCfontID'] > 0 || $this->fonts[$fontkey]['useOTL'] > 0) { // mPDF 5.7.1
						// First see if there is a cached compressed file
						if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.ps.z')) {
							$f = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.ps.z', 'rb');
							if (!$f) {
								$this->Error('Font file .ps.z not found');
							}
							while (!feof($f)) {
								$font .= fread($f, 2048);
							}
							fclose($f);
							include(_MPDF_TTFONTDATAPATH . $fontkey . '.ps.php'); // sets $originalsize (of repackaged font)
						} else {
							if (!class_exists('TTFontFile', false)) {
								include(_MPDF_PATH . 'classes/ttfontsuni.php');
							}
							$ttf = new TTFontFile();
							$font = $ttf->repackageTTF($this->FontFiles[$fontkey]['ttffile'], $this->fonts[$fontkey]['TTCfontID'], $this->debugfonts, $this->fonts[$fontkey]['useOTL']); // mPDF 5.7.1

							$originalsize = strlen($font);
							$font = gzcompress($font);
							unset($ttf);
							if (is_writable(dirname(_MPDF_TTFONTDATAPATH . 'x'))) {
								$fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.ps.z', "wb");
								fwrite($fh, $font, strlen($font));
								fclose($fh);
								$fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.ps.php', "wb");
								$len = "<?php \n";
								$len.='$originalsize=' . $originalsize . ";\n";
								$len.="?>";
								fwrite($fh, $len, strlen($len));
								fclose($fh);
							}
						}
					} else {
						// First see if there is a cached compressed file
						if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.z')) {
							$f = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.z', 'rb');
							if (!$f) {
								$this->Error('Font file not found');
							}
							while (!feof($f)) {
								$font .= fread($f, 2048);
							}
							fclose($f);
						} else {
							$f = fopen($this->FontFiles[$fontkey]['ttffile'], 'rb');
							if (!$f) {
								$this->Error('Font file not found');
							}
							while (!feof($f)) {
								$font .= fread($f, 2048);
							}
							fclose($f);
							$font = gzcompress($font);
							if (is_writable(dirname(_MPDF_TTFONTDATAPATH . 'x'))) {
								$fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.z', "wb");
								fwrite($fh, $font, strlen($font));
								fclose($fh);
							}
						}
					}

					$this->_out('<</Length ' . strlen($font));
					$this->_out('/Filter /FlateDecode');
					$this->_out('/Length1 ' . $originalsize);
					$this->_out('>>');
					$this->_putstream($font);
					$this->_out('endobj');
				}
			}
		}

		$nfonts = count($this->fonts);
		$fctr = 1;
		foreach ($this->fonts as $k => $font) {
			//Font objects
			$type = $font['type'];
			$name = $font['name'];
			if ((!isset($font['used']) || !$font['used']) && $type == 'TTF') {
				continue;
			}
			if ($this->progressBar) {
				$this->UpdateProgressBar(2, intval($fctr * 100 / $nfonts), 'Writing Fonts');
				$fctr++;
			} // *PROGRESS-BAR*
			if (isset($font['asSubset'])) {
				$asSubset = $font['asSubset'];
			} else {
				$asSubset = '';
			}
			/* -- CJK-FONTS -- */
			if ($type == 'Type0') {  // = Adobe CJK Fonts
				$this->fonts[$k]['n'] = $this->n + 1;
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_putType0($font);
			} else
			/* -- END CJK-FONTS -- */
			if ($type == 'core') {
				//Standard font
				$this->fonts[$k]['n'] = $this->n + 1;
				if ($this->PDFA || $this->PDFX) {
					$this->Error('Core fonts are not allowed in PDF/A1-b or PDFX/1-a files (Times, Helvetica, Courier etc.)');
				}
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /' . $name);
				$this->_out('/Subtype /Type1');
				if ($name != 'Symbol' && $name != 'ZapfDingbats') {
					$this->_out('/Encoding /WinAnsiEncoding');
				}
				$this->_out('>>');
				$this->_out('endobj');
			}
			// TrueType embedded SUBSETS for SIP (CJK extB containing Supplementary Ideographic Plane 2)
			// Or Unicode Plane 1 - Supplementary Multilingual Plane
			else if ($type == 'TTF' && ($font['sip'] || $font['smp'])) {
				if (!$font['used']) {
					continue;
				}
				$ssfaid = "AA";
				if (!class_exists('TTFontFile', false)) {
					include(_MPDF_PATH . 'classes/ttfontsuni.php');
				}
				$ttf = new TTFontFile();
				for ($sfid = 0; $sfid < count($font['subsetfontids']); $sfid++) {
					$this->fonts[$k]['n'][$sfid] = $this->n + 1;  // NB an array for subset
					$subsetname = 'MPDF' . $ssfaid . '+' . $font['name'];
					$ssfaid++;

					/* For some strange reason a subset ($sfid > 0) containing less than 97 characters causes an error
					  so fill up the array */
					for ($j = count($font['subsets'][$sfid]); $j < 98; $j++) {
						$font['subsets'][$sfid][$j] = 0;
					}

					$subset = $font['subsets'][$sfid];
					unset($subset[0]);
					$ttfontstream = $ttf->makeSubsetSIP($font['ttffile'], $subset, $font['TTCfontID'], $this->debugfonts, $font['useOTL']); // mPDF 5.7.1
					$ttfontsize = strlen($ttfontstream);
					$fontstream = gzcompress($ttfontstream);
					$widthstring = '';
					$toUnistring = '';


					foreach ($font['subsets'][$sfid] AS $cp => $u) {
						$w = $this->_getCharWidth($font['cw'], $u);
						if ($w !== false) {
							$widthstring .= $w . ' ';
						} else {
							$widthstring .= round($ttf->defaultWidth) . ' ';
						}
						if ($u > 65535) {
							$utf8 = chr(($u >> 18) + 240) . chr((($u >> 12) & 63) + 128) . chr((($u >> 6) & 63) + 128) . chr(($u & 63) + 128);
							$utf16 = mb_convert_encoding($utf8, 'UTF-16BE', 'UTF-8');
							$l1 = ord($utf16[0]);
							$h1 = ord($utf16[1]);
							$l2 = ord($utf16[2]);
							$h2 = ord($utf16[3]);
							$toUnistring .= sprintf("<%02s> <%02s%02s%02s%02s>\n", strtoupper(dechex($cp)), strtoupper(dechex($l1)), strtoupper(dechex($h1)), strtoupper(dechex($l2)), strtoupper(dechex($h2)));
						} else {
							$toUnistring .= sprintf("<%02s> <%04s>\n", strtoupper(dechex($cp)), strtoupper(dechex($u)));
						}
					}

					//Additional Type1 or TrueType font
					$this->_newobj();
					$this->_out('<</Type /Font');
					$this->_out('/BaseFont /' . $subsetname);
					$this->_out('/Subtype /TrueType');
					$this->_out('/FirstChar 0 /LastChar ' . (count($font['subsets'][$sfid]) - 1));
					$this->_out('/Widths ' . ($this->n + 1) . ' 0 R');
					$this->_out('/FontDescriptor ' . ($this->n + 2) . ' 0 R');
					$this->_out('/ToUnicode ' . ($this->n + 3) . ' 0 R');
					$this->_out('>>');
					$this->_out('endobj');

					//Widths
					$this->_newobj();
					$this->_out('[' . $widthstring . ']');
					$this->_out('endobj');

					//Descriptor
					$this->_newobj();
					$s = '<</Type /FontDescriptor /FontName /' . $subsetname . "\n";
					foreach ($font['desc'] as $kd => $v) {
						if ($kd == 'Flags') {
							$v = $v | 4;
							$v = $v & ~32;
						} // SYMBOLIC font flag
						$s.=' /' . $kd . ' ' . $v . "\n";
					}
					$s.='/FontFile2 ' . ($this->n + 2) . ' 0 R';
					$this->_out($s . '>>');
					$this->_out('endobj');

					// ToUnicode
					$this->_newobj();
					$toUni = "/CIDInit /ProcSet findresource begin\n";
					$toUni .= "12 dict begin\n";
					$toUni .= "begincmap\n";
					$toUni .= "/CIDSystemInfo\n";
					$toUni .= "<</Registry (Adobe)\n";
					$toUni .= "/Ordering (UCS)\n";
					$toUni .= "/Supplement 0\n";
					$toUni .= ">> def\n";
					$toUni .= "/CMapName /Adobe-Identity-UCS def\n";
					$toUni .= "/CMapType 2 def\n";
					$toUni .= "1 begincodespacerange\n";
					$toUni .= "<00> <FF>\n";
					//$toUni .= sprintf("<00> <%02s>\n", strtoupper(dechex(count($font['subsets'][$sfid])-1)));
					$toUni .= "endcodespacerange\n";
					$toUni .= count($font['subsets'][$sfid]) . " beginbfchar\n";
					$toUni .= $toUnistring;
					$toUni .= "endbfchar\n";
					$toUni .= "endcmap\n";
					$toUni .= "CMapName currentdict /CMap defineresource pop\n";
					$toUni .= "end\n";
					$toUni .= "end\n";
					$this->_out('<</Length ' . (strlen($toUni)) . '>>');
					$this->_putstream($toUni);
					$this->_out('endobj');

					//Font file
					$this->_newobj();
					$this->_out('<</Length ' . strlen($fontstream));
					$this->_out('/Filter /FlateDecode');
					$this->_out('/Length1 ' . $ttfontsize);
					$this->_out('>>');
					$this->_putstream($fontstream);
					$this->_out('endobj');
				} // foreach subset
				unset($ttf);
			}
			// TrueType embedded SUBSETS or FULL
			else if ($type == 'TTF') {
				$this->fonts[$k]['n'] = $this->n + 1;
				if ($asSubset) {
					$ssfaid = "A";
					if (!class_exists('TTFontFile', false)) {
						include(_MPDF_PATH . 'classes/ttfontsuni.php');
					}
					$ttf = new TTFontFile();
					$fontname = 'MPDFA' . $ssfaid . '+' . $font['name'];
					$subset = $font['subset'];
					unset($subset[0]);
					$ttfontstream = $ttf->makeSubset($font['ttffile'], $subset, $font['TTCfontID'], $this->debugfonts, $font['useOTL']);
					$ttfontsize = strlen($ttfontstream);
					$fontstream = gzcompress($ttfontstream);
					$codeToGlyph = $ttf->codeToGlyph;
					unset($codeToGlyph[0]);
				} else {
					$fontname = $font['name'];
				}
				// Type0 Font
				// A composite font - a font composed of other fonts, organized hierarchically
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/Subtype /Type0');
				$this->_out('/BaseFont /' . $fontname . '');
				$this->_out('/Encoding /Identity-H');
				$this->_out('/DescendantFonts [' . ($this->n + 1) . ' 0 R]');
				$this->_out('/ToUnicode ' . ($this->n + 2) . ' 0 R');
				$this->_out('>>');
				$this->_out('endobj');

				// CIDFontType2
				// A CIDFont whose glyph descriptions are based on TrueType font technology
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/Subtype /CIDFontType2');
				$this->_out('/BaseFont /' . $fontname . '');
				$this->_out('/CIDSystemInfo ' . ($this->n + 2) . ' 0 R');
				$this->_out('/FontDescriptor ' . ($this->n + 3) . ' 0 R');
				if (isset($font['desc']['MissingWidth'])) {
					$this->_out('/DW ' . $font['desc']['MissingWidth'] . '');
				}

				if (!$asSubset && file_exists(_MPDF_TTFONTDATAPATH . $font['fontkey'] . '.cw')) {
					$w = '';
					$w = file_get_contents(_MPDF_TTFONTDATAPATH . $font['fontkey'] . '.cw');
					$this->_out($w);
				} else {
					$this->_putTTfontwidths($font, $asSubset, ($asSubset ? $ttf->maxUni : 0));
				}

				$this->_out('/CIDToGIDMap ' . ($this->n + 4) . ' 0 R');
				$this->_out('>>');
				$this->_out('endobj');

				// ToUnicode
				$this->_newobj();
				$toUni = "/CIDInit /ProcSet findresource begin\n";
				$toUni .= "12 dict begin\n";
				$toUni .= "begincmap\n";
				$toUni .= "/CIDSystemInfo\n";
				$toUni .= "<</Registry (Adobe)\n";
				$toUni .= "/Ordering (UCS)\n";
				$toUni .= "/Supplement 0\n";
				$toUni .= ">> def\n";
				$toUni .= "/CMapName /Adobe-Identity-UCS def\n";
				$toUni .= "/CMapType 2 def\n";
				$toUni .= "1 begincodespacerange\n";
				$toUni .= "<0000> <FFFF>\n";
				$toUni .= "endcodespacerange\n";
				$toUni .= "1 beginbfrange\n";
				$toUni .= "<0000> <FFFF> <0000>\n";
				$toUni .= "endbfrange\n";
				$toUni .= "endcmap\n";
				$toUni .= "CMapName currentdict /CMap defineresource pop\n";
				$toUni .= "end\n";
				$toUni .= "end\n";
				$this->_out('<</Length ' . (strlen($toUni)) . '>>');
				$this->_putstream($toUni);
				$this->_out('endobj');


				// CIDSystemInfo dictionary
				$this->_newobj();
				$this->_out('<</Registry (Adobe)');
				$this->_out('/Ordering (UCS)');
				$this->_out('/Supplement 0');
				$this->_out('>>');
				$this->_out('endobj');

				// Font descriptor
				$this->_newobj();
				$this->_out('<</Type /FontDescriptor');
				$this->_out('/FontName /' . $fontname);
				foreach ($font['desc'] as $kd => $v) {
					if ($asSubset && $kd == 'Flags') {
						$v = $v | 4;
						$v = $v & ~32;
					} // SYMBOLIC font flag
					$this->_out(' /' . $kd . ' ' . $v);
				}
				if ($font['panose']) {
					$this->_out(' /Style << /Panose <' . $font['panose'] . '> >>');
				}
				if ($asSubset) {
					$this->_out('/FontFile2 ' . ($this->n + 2) . ' 0 R');
				} else if ($font['fontkey']) {
					// obj ID of a stream containing a TrueType font program
					$this->_out('/FontFile2 ' . $this->FontFiles[$font['fontkey']]['n'] . ' 0 R');
				}
				$this->_out('>>');
				$this->_out('endobj');

				// Embed CIDToGIDMap
				// A specification of the mapping from CIDs to glyph indices
				if ($asSubset) {
					$cidtogidmap = '';
					$cidtogidmap = str_pad('', 256 * 256 * 2, "\x00");
					foreach ($codeToGlyph as $cc => $glyph) {
						$cidtogidmap[$cc * 2] = chr($glyph >> 8);
						$cidtogidmap[$cc * 2 + 1] = chr($glyph & 0xFF);
					}
					$cidtogidmap = gzcompress($cidtogidmap);
				} else {
					// First see if there is a cached CIDToGIDMapfile
					$cidtogidmap = '';
					if (file_exists(_MPDF_TTFONTDATAPATH . $font['fontkey'] . '.cgm')) {
						$f = fopen(_MPDF_TTFONTDATAPATH . $font['fontkey'] . '.cgm', 'rb');
						while (!feof($f)) {
							$cidtogidmap .= fread($f, 2048);
						}
						fclose($f);
					} else {
						if (!class_exists('TTFontFile', false)) {
							include(_MPDF_PATH . 'classes/ttfontsuni.php');
						}
						$ttf = new TTFontFile();
						$charToGlyph = $ttf->getCTG($font['ttffile'], $font['TTCfontID'], $this->debugfonts, $font['useOTL']);
						$cidtogidmap = str_pad('', 256 * 256 * 2, "\x00");
						foreach ($charToGlyph as $cc => $glyph) {
							$cidtogidmap[$cc * 2] = chr($glyph >> 8);
							$cidtogidmap[$cc * 2 + 1] = chr($glyph & 0xFF);
						}
						unset($ttf);
						$cidtogidmap = gzcompress($cidtogidmap);
						if (is_writable(dirname(_MPDF_TTFONTDATAPATH . 'x'))) {
							$fh = fopen(_MPDF_TTFONTDATAPATH . $font['fontkey'] . '.cgm', "wb");
							fwrite($fh, $cidtogidmap, strlen($cidtogidmap));
							fclose($fh);
						}
					}
				}
				$this->_newobj();
				$this->_out('<</Length ' . strlen($cidtogidmap) . '');
				$this->_out('/Filter /FlateDecode');
				$this->_out('>>');
				$this->_putstream($cidtogidmap);
				$this->_out('endobj');

				//Font file
				if ($asSubset) {
					$this->_newobj();
					$this->_out('<</Length ' . strlen($fontstream));
					$this->_out('/Filter /FlateDecode');
					$this->_out('/Length1 ' . $ttfontsize);
					$this->_out('>>');
					$this->_putstream($fontstream);
					$this->_out('endobj');
					unset($ttf);
				}
			} else {
				$this->Error('Unsupported font type: ' . $type . ' (' . $name . ')');
			}
		}
	}


	/* -- CJK-FONTS -- */

// from class PDF_Chinese CJK EXTENSIONS
	

	/* -- END CJK-FONTS -- */

	function _putimages()
	{
		$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
		reset($this->images);
		while (list($file, $info) = each($this->images)) {
			$this->_newobj();
			$this->images[$file]['n'] = $this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Image');
			$this->_out('/Width ' . $info['w']);
			$this->_out('/Height ' . $info['h']);
			if (isset($info['interpolation']) && $info['interpolation']) {
				$this->_out('/Interpolate true'); // mPDF 6 - image interpolation shall be performed by a conforming reader
			}
			if (isset($info['masked'])) {
				$this->_out('/SMask ' . ($this->n - 1) . ' 0 R');
			}
			// set color space
			$icc = false;
			if (isset($info['icc']) AND ( $info['icc'] !== false)) {
				// ICC Colour Space
				$icc = true;
				$this->_out('/ColorSpace [/ICCBased ' . ($this->n + 1) . ' 0 R]');
			} else if ($info['cs'] == 'Indexed') {
				if ($this->PDFX || ($this->PDFA && $this->restrictColorSpace == 3)) {
					$this->Error("PDFA1-b and PDFX/1-a files do not permit using mixed colour space (" . $file . ").");
				}
				$this->_out('/ColorSpace [/Indexed /DeviceRGB ' . (strlen($info['pal']) / 3 - 1) . ' ' . ($this->n + 1) . ' 0 R]');
			} else {
				$this->_out('/ColorSpace /' . $info['cs']);
				if ($info['cs'] == 'DeviceCMYK') {
					if ($this->PDFA && $this->restrictColorSpace != 3) {
						$this->Error("PDFA1-b does not permit Images using mixed colour space (" . $file . ").");
					}
					if ($info['type'] == 'jpg') {
						$this->_out('/Decode [1 0 1 0 1 0 1 0]');
					}
				} else if ($info['cs'] == 'DeviceRGB' && ($this->PDFX || ($this->PDFA && $this->restrictColorSpace == 3))) {
					$this->Error("PDFA1-b and PDFX/1-a files do not permit using mixed colour space (" . $file . ").");
				}
			}
			$this->_out('/BitsPerComponent ' . $info['bpc']);
			if (isset($info['f']) && $info['f']) {
				$this->_out('/Filter /' . $info['f']);
			}
			if (isset($info['parms'])) {
				$this->_out($info['parms']);
			}
			if (isset($info['trns']) and is_array($info['trns'])) {
				$trns = '';
				for ($i = 0; $i < count($info['trns']); $i++)
					$trns.=$info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
				$this->_out('/Mask [' . $trns . ']');
			}
			$this->_out('/Length ' . strlen($info['data']) . '>>');
			$this->_putstream($info['data']);
			unset($this->images[$file]['data']);
			$this->_out('endobj');

			// ICC colour profile
			if ($icc) {
				$this->_newobj();
				$icc = ($this->compress) ? gzcompress($info['icc']) : $info['icc'];
				$this->_out('<</N ' . $info['ch'] . ' ' . $filter . '/Length ' . strlen($icc) . '>>');
				$this->_putstream($icc);
				$this->_out('endobj');
			}
			//Palette
			else if ($info['cs'] == 'Indexed') {
				$this->_newobj();
				$pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
				$this->_out('<<' . $filter . '/Length ' . strlen($pal) . '>>');
				$this->_putstream($pal);
				$this->_out('endobj');
			}
		}
	}

	function _putinfo()
	{
		$this->_out('/Producer ' . $this->_UTF16BEtextstring('mPDF ' . mPDF_VERSION));
		if (!empty($this->title))
			$this->_out('/Title ' . $this->_UTF16BEtextstring($this->title));
		if (!empty($this->subject))
			$this->_out('/Subject ' . $this->_UTF16BEtextstring($this->subject));
		if (!empty($this->author))
			$this->_out('/Author ' . $this->_UTF16BEtextstring($this->author));
		if (!empty($this->keywords))
			$this->_out('/Keywords ' . $this->_UTF16BEtextstring($this->keywords));
		if (!empty($this->creator))
			$this->_out('/Creator ' . $this->_UTF16BEtextstring($this->creator));

		$z = date('O'); // +0200
		$offset = substr($z, 0, 3) . "'" . substr($z, 3, 2) . "'";
		$this->_out('/CreationDate ' . $this->_textstring(date('YmdHis') . $offset));
		$this->_out('/ModDate ' . $this->_textstring(date('YmdHis') . $offset));
		if ($this->PDFX) {
			$this->_out('/Trapped/False');
			$this->_out('/GTS_PDFXVersion(PDF/X-1a:2003)');
		}
	}

	function _putmetadata()
	{
		$this->_newobj();
		$this->MetadataRoot = $this->n;
		$Producer = 'aGrobak ' . mPDF_VERSION;
		$z = date('O'); // +0200
		$offset = substr($z, 0, 3) . ':' . substr($z, 3, 2);
		$CreationDate = date('Y-m-d\TH:i:s') . $offset; // 2006-03-10T10:47:26-05:00 2006-06-19T09:05:17Z
		$uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));


		$m = '<?xpacket begin="' . chr(239) . chr(187) . chr(191) . '" id="W5M0MpCehiHzreSzNTczkc9d"?>' . "\n"; // begin = FEFF BOM
		$m .= ' <x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="3.1-701">' . "\n";
		$m .= '  <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";
		$m .= '   <rdf:Description rdf:about="uuid:' . $uuid . '" xmlns:pdf="http://ns.adobe.com/pdf/1.3/">' . "\n";
		$m .= '    <pdf:Producer>' . $Producer . '</pdf:Producer>' . "\n";
		if (!empty($this->keywords)) {
			$m .= '    <pdf:Keywords>' . $this->keywords . '</pdf:Keywords>' . "\n";
		}
		$m .= '   </rdf:Description>' . "\n";

		$m .= '   <rdf:Description rdf:about="uuid:' . $uuid . '" xmlns:xmp="http://ns.adobe.com/xap/1.0/">' . "\n";
		$m .= '    <xmp:CreateDate>' . $CreationDate . '</xmp:CreateDate>' . "\n";
		$m .= '    <xmp:ModifyDate>' . $CreationDate . '</xmp:ModifyDate>' . "\n";
		$m .= '    <xmp:MetadataDate>' . $CreationDate . '</xmp:MetadataDate>' . "\n";
		if (!empty($this->creator)) {
			$m .= '    <xmp:CreatorTool>' . $this->creator . '</xmp:CreatorTool>' . "\n";
		}
		$m .= '   </rdf:Description>' . "\n";

		// DC elements
		$m .= '   <rdf:Description rdf:about="uuid:' . $uuid . '" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
		$m .= '    <dc:format>application/pdf</dc:format>' . "\n";
		if (!empty($this->title)) {
			$m .= '    <dc:title>
	 <rdf:Alt>
	  <rdf:li xml:lang="x-default">' . $this->title . '</rdf:li>
	 </rdf:Alt>
	</dc:title>' . "\n";
		}
		if (!empty($this->keywords)) {
			$m .= '    <dc:subject>
	 <rdf:Bag>
	  <rdf:li>' . $this->keywords . '</rdf:li>
	 </rdf:Bag>
	</dc:subject>' . "\n";
		}
		if (!empty($this->subject)) {
			$m .= '    <dc:description>
	 <rdf:Alt>
	  <rdf:li xml:lang="x-default">' . $this->subject . '</rdf:li>
	 </rdf:Alt>
	</dc:description>' . "\n";
		}
		if (!empty($this->author)) {
			$m .= '    <dc:creator>
	 <rdf:Seq>
	  <rdf:li>' . $this->author . '</rdf:li>
	 </rdf:Seq>
	</dc:creator>' . "\n";
		}
		$m .= '   </rdf:Description>' . "\n";


		// This bit is specific to PDFX-1a
		if ($this->PDFX) {
			$m .= '   <rdf:Description rdf:about="uuid:' . $uuid . '" xmlns:pdfx="http://ns.adobe.com/pdfx/1.3/" pdfx:Apag_PDFX_Checkup="1.3" pdfx:GTS_PDFXConformance="PDF/X-1a:2003" pdfx:GTS_PDFXVersion="PDF/X-1:2003"/>' . "\n";
		}

		// This bit is specific to PDFA-1b
		else if ($this->PDFA) {
			$m .= '   <rdf:Description rdf:about="uuid:' . $uuid . '" xmlns:pdfaid="http://www.aiim.org/pdfa/ns/id/" >' . "\n";
			$m .= '    <pdfaid:part>1</pdfaid:part>' . "\n";
			$m .= '    <pdfaid:conformance>B</pdfaid:conformance>' . "\n";
			$m .= '    <pdfaid:amd>2005</pdfaid:amd>' . "\n";
			$m .= '   </rdf:Description>' . "\n";
		}

		$m .= '   <rdf:Description rdf:about="uuid:' . $uuid . '" xmlns:xmpMM="http://ns.adobe.com/xap/1.0/mm/">' . "\n";
		$m .= '    <xmpMM:DocumentID>uuid:' . $uuid . '</xmpMM:DocumentID>' . "\n";
		$m .= '   </rdf:Description>' . "\n";
		$m .= '  </rdf:RDF>' . "\n";
		$m .= ' </x:xmpmeta>' . "\n";
		$m .= str_repeat(str_repeat(' ', 100) . "\n", 20); // 2-4kB whitespace padding required
		$m .= '<?xpacket end="w"?>'; // "r" read only
		$this->_out('<</Type/Metadata/Subtype/XML/Length ' . strlen($m) . '>>');
		$this->_putstream($m);
		$this->_out('endobj');
	}



	function _putcatalog()
	{
		$this->_out('/Type /Catalog');
		$this->_out('/Pages 1 0 R');
		if ($this->ZoomMode == 'fullpage')
			$this->_out('/OpenAction [3 0 R /Fit]');
		elseif ($this->ZoomMode == 'fullwidth')
			$this->_out('/OpenAction [3 0 R /FitH null]');
		elseif ($this->ZoomMode == 'real')
			$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
		elseif (!is_string($this->ZoomMode))
			$this->_out('/OpenAction [3 0 R /XYZ null null ' . ($this->ZoomMode / 100) . ']');
		else
			$this->_out('/OpenAction [3 0 R /XYZ null null null]');
		if ($this->LayoutMode == 'single')
			$this->_out('/PageLayout /SinglePage');
		elseif ($this->LayoutMode == 'continuous')
			$this->_out('/PageLayout /OneColumn');
		elseif ($this->LayoutMode == 'twoleft')
			$this->_out('/PageLayout /TwoColumnLeft');
		elseif ($this->LayoutMode == 'tworight')
			$this->_out('/PageLayout /TwoColumnRight');
		elseif ($this->LayoutMode == 'two') {
			if ($this->mirrorMargins) {
				$this->_out('/PageLayout /TwoColumnRight');
			} else {
				$this->_out('/PageLayout /TwoColumnLeft');
			}
		}

		/* -- BOOKMARKS -- */
		if (count($this->BMoutlines) > 0) {
			$this->_out('/Outlines ' . $this->OutlineRoot . ' 0 R');
			$this->_out('/PageMode /UseOutlines');
		}
		/* -- END BOOKMARKS -- */
		if (is_int(strpos($this->DisplayPreferences, 'FullScreen')))
			$this->_out('/PageMode /FullScreen');

		// Metadata
		if ($this->PDFA || $this->PDFX) {
			$this->_out('/Metadata ' . $this->MetadataRoot . ' 0 R');
		}
		// OutputIntents
		if ($this->PDFA || $this->PDFX || $this->ICCProfile) {
			$this->_out('/OutputIntents [' . $this->OutputIntentRoot . ' 0 R]');
		}

		/* -- FORMS -- */
		if (count($this->mpdfform->forms) > 0) {
			$this->mpdfform->_putFormsCatalog();
		}
		/* -- END FORMS -- */
		if (isset($this->js)) {
			$this->_out('/Names << /JavaScript ' . ($this->n_js) . ' 0 R >> ');
		}

		if ($this->DisplayPreferences || $this->directionality == 'rtl' || $this->mirrorMargins) {
			$this->_out('/ViewerPreferences<<');
			if (is_int(strpos($this->DisplayPreferences, 'HideMenubar')))
				$this->_out('/HideMenubar true');
			if (is_int(strpos($this->DisplayPreferences, 'HideToolbar')))
				$this->_out('/HideToolbar true');
			if (is_int(strpos($this->DisplayPreferences, 'HideWindowUI')))
				$this->_out('/HideWindowUI true');
			if (is_int(strpos($this->DisplayPreferences, 'DisplayDocTitle')))
				$this->_out('/DisplayDocTitle true');
			if (is_int(strpos($this->DisplayPreferences, 'CenterWindow')))
				$this->_out('/CenterWindow true');
			if (is_int(strpos($this->DisplayPreferences, 'FitWindow')))
				$this->_out('/FitWindow true');
			// /PrintScaling is PDF 1.6 spec.
			if (is_int(strpos($this->DisplayPreferences, 'NoPrintScaling')) && !$this->PDFA && !$this->PDFX)
				$this->_out('/PrintScaling /None');
			if ($this->directionality == 'rtl')
				$this->_out('/Direction /R2L');
			// /Duplex is PDF 1.7 spec.
			if ($this->mirrorMargins && !$this->PDFA && !$this->PDFX) {
				// if ($this->DefOrientation=='P') $this->_out('/Duplex /DuplexFlipShortEdge');
				$this->_out('/Duplex /DuplexFlipLongEdge'); // PDF v1.7+
			}
			$this->_out('>>');
		}
		if ($this->open_layer_pane && ($this->hasOC || count($this->layers)))
			$this->_out('/PageMode /UseOC');

		if ($this->hasOC || count($this->layers)) {
			$p = $v = $h = $l = $loff = $lall = $as = '';
			if ($this->hasOC) {
				if (($this->hasOC & 1) == 1)
					$p = $this->n_ocg_print . ' 0 R';
				if (($this->hasOC & 2) == 2)
					$v = $this->n_ocg_view . ' 0 R';
				if (($this->hasOC & 4) == 4)
					$h = $this->n_ocg_hidden . ' 0 R';
				$as = "<</Event /Print /OCGs [$p $v $h] /Category [/Print]>> <</Event /View /OCGs [$p $v $h] /Category [/View]>>";
			}

			if (count($this->layers)) {
				foreach ($this->layers as $k => $layer) {
					if (strtolower($this->layerDetails[$k]['state']) == 'hidden') {
						$loff .= $layer['n'] . ' 0 R ';
					} else {
						$l .= $layer['n'] . ' 0 R ';
					}
					$lall .= $layer['n'] . ' 0 R ';
				}
			}
			$this->_out("/OCProperties <</OCGs [$p $v $h $lall] /D <</ON [$p $l] /OFF [$v $h $loff] ");
			$this->_out("/Order [$v $p $h $lall] ");
			if ($as)
				$this->_out("/AS [$as] ");
			$this->_out(">>>>");
		}
	}

// Inactive function left for backwards compatability
	function SetUserRights($enable = true, $annots = "", $form = "", $signature = "")
	{
		// Does nothing
	}

	function _enddoc()
	{
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '10', 'Writing Headers & Footers');
		} // *PROGRESS-BAR*
		$this->_puthtmlheaders();
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '20', 'Writing Pages');
		} // *PROGRESS-BAR*
		// Remove references to unused fonts (usually default font)
		foreach ($this->fonts as $fk => $font) {
			if (isset($font['type']) && $font['type'] == 'TTF' && !$font['used']) {
				if ($font['sip'] || $font['smp']) {
					foreach ($font['subsetfontids'] AS $k => $fid) {
						foreach ($this->pages AS $pn => $page) {
							$this->pages[$pn] = preg_replace('/\s\/F' . $fid . ' \d[\d.]* Tf\s/is', ' ', $this->pages[$pn]);
						}
					}
				} else {
					foreach ($this->pages AS $pn => $page) {
						$this->pages[$pn] = preg_replace('/\s\/F' . $font['i'] . ' \d[\d.]* Tf\s/is', ' ', $this->pages[$pn]);
					}
				}
			}
		}

		if (count($this->layers)) {
			foreach ($this->pages AS $pn => $page) {
				preg_match_all('/\/OCZ-index \/ZI(\d+) BDC(.*?)(EMCZ)-index/is', $this->pages[$pn], $m1);
				preg_match_all('/\/OCBZ-index \/ZI(\d+) BDC(.*?)(EMCBZ)-index/is', $this->pages[$pn], $m2);
				preg_match_all('/\/OCGZ-index \/ZI(\d+) BDC(.*?)(EMCGZ)-index/is', $this->pages[$pn], $m3);
				$m = array();
				for ($i = 0; $i < 4; $i++) {
					$m[$i] = array_merge($m1[$i], $m2[$i], $m3[$i]);
				}
				if (count($m[0])) {
					$sortarr = array();
					for ($i = 0; $i < count($m[0]); $i++) {
						$key = $m[1][$i] * 2;
						if ($m[3][$i] == 'EMCZ')
							$key +=2; // background first then gradient then normal
						else if ($m[3][$i] == 'EMCGZ')
							$key +=1;
						$sortarr[$i] = $key;
					}
					asort($sortarr);
					foreach ($sortarr AS $i => $k) {
						$this->pages[$pn] = str_replace($m[0][$i], '', $this->pages[$pn]);
						$this->pages[$pn] .= "\n" . $m[0][$i] . "\n";
					}
					$this->pages[$pn] = preg_replace('/\/OC[BG]{0,1}Z-index \/ZI(\d+) BDC/is', '/OC /ZI\\1 BDC ', $this->pages[$pn]);
					$this->pages[$pn] = preg_replace('/EMC[BG]{0,1}Z-index/is', 'EMC', $this->pages[$pn]);
				}
			}
		}

		$this->_putpages();
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '30', 'Writing document resources');
		} // *PROGRESS-BAR*

		$this->_putresources();
		//Info
		$this->_newobj();
		$this->InfoRoot = $this->n;
		$this->_out('<<');
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '80', 'Writing document info');
		} // *PROGRESS-BAR*
		$this->_putinfo();
		$this->_out('>>');
		$this->_out('endobj');

		// METADATA
		if ($this->PDFA || $this->PDFX) {
			$this->_putmetadata();
		}
		// OUTPUTINTENT
		if ($this->PDFA || $this->PDFX || $this->ICCProfile) {
			$this->_putoutputintent();
		}

		//Catalog
		$this->_newobj();
		$this->_out('<<');
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '90', 'Writing document catalog');
		} // *PROGRESS-BAR*
		$this->_putcatalog();
		$this->_out('>>');
		$this->_out('endobj');
		//Cross-ref
		$o = strlen($this->buffer);
		$this->_out('xref');
		$this->_out('0 ' . ($this->n + 1));
		$this->_out('0000000000 65535 f ');
		for ($i = 1; $i <= $this->n; $i++)
			$this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
		//Trailer
		$this->_out('trailer');
		$this->_out('<<');
		$this->_puttrailer();
		$this->_out('>>');
		$this->_out('startxref');
		$this->_out($o);

		$this->buffer .= '%%EOF';
		$this->state = 3;
		/* -- IMPORTS -- */

		if ($this->enableImports && count($this->parsers) > 0) {
			foreach ($this->parsers as $k => $_) {
				$this->parsers[$k]->closeFile();
				$this->parsers[$k] = null;
				unset($this->parsers[$k]);
			}
		}
		/* -- END IMPORTS -- */
	}

	function _beginpage($orientation, $mgl = '', $mgr = '', $mgt = '', $mgb = '', $mgh = '', $mgf = '', $ohname = '', $ehname = '', $ofname = '', $efname = '', $ohvalue = 0, $ehvalue = 0, $ofvalue = 0, $efvalue = 0, $pagesel = '', $newformat = '')
	{
		if (!($pagesel && $this->page == 1 && (sprintf("%0.4f", $this->y) == sprintf("%0.4f", $this->tMargin)))) {
			$this->page++;
			$this->pages[$this->page] = '';
		}
		$this->state = 2;
		$resetHTMLHeadersrequired = false;

		if ($newformat) {
			$this->_setPageSize($newformat, $orientation);
		}
		/* -- CSS-PAGE -- */
		// Paged media (page-box)
		if ($pagesel || (isset($this->page_box['using']) && $this->page_box['using'])) {
			if ($pagesel || $this->page == 1) {
				$first = true;
			} else {
				$first = false;
			}
			if ($this->mirrorMargins && ($this->page % 2 == 0)) {
				$oddEven = 'E';
			} else {
				$oddEven = 'O';
			}
			if ($pagesel) {
				$psel = $pagesel;
			} else if ($this->page_box['current']) {
				$psel = $this->page_box['current'];
			} else {
				$psel = '';
			}
			list($orientation, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $hname, $fname, $bg, $resetpagenum, $pagenumstyle, $suppress, $marks, $newformat) = $this->SetPagedMediaCSS($psel, $first, $oddEven);
			if ($this->mirrorMargins && ($this->page % 2 == 0)) {
				if ($hname) {
					$ehvalue = 1;
					$ehname = $hname;
				} else {
					$ehvalue = -1;
				}
				if ($fname) {
					$efvalue = 1;
					$efname = $fname;
				} else {
					$efvalue = -1;
				}
			} else {
				if ($hname) {
					$ohvalue = 1;
					$ohname = $hname;
				} else {
					$ohvalue = -1;
				}
				if ($fname) {
					$ofvalue = 1;
					$ofname = $fname;
				} else {
					$ofvalue = -1;
				}
			}
			if ($resetpagenum || $pagenumstyle || $suppress) {
				$this->PageNumSubstitutions[] = array('from' => ($this->page), 'reset' => $resetpagenum, 'type' => $pagenumstyle, 'suppress' => $suppress);
			}
			// PAGED MEDIA - CROP / CROSS MARKS from @PAGE
			$this->show_marks = $marks;

			// Background color
			if (isset($bg['BACKGROUND-COLOR'])) {
				$cor = $this->ConvertColor($bg['BACKGROUND-COLOR']);
				if ($cor) {
					$this->bodyBackgroundColor = $cor;
				}
			} else {
				$this->bodyBackgroundColor = false;
			}

			/* -- BACKGROUNDS -- */
			if (isset($bg['BACKGROUND-GRADIENT'])) {
				$this->bodyBackgroundGradient = $bg['BACKGROUND-GRADIENT'];
			} else {
				$this->bodyBackgroundGradient = false;
			}

			// Tiling Patterns
			if (isset($bg['BACKGROUND-IMAGE']) && $bg['BACKGROUND-IMAGE']) {
				$ret = $this->SetBackground($bg, $this->pgwidth);
				if ($ret) {
					$this->bodyBackgroundImage = $ret;
				}
			} else {
				$this->bodyBackgroundImage = false;
			}
			/* -- END BACKGROUNDS -- */

			$this->page_box['current'] = $psel;
			$this->page_box['using'] = true;
		}
		/* -- END CSS-PAGE -- */

		//Page orientation
		if (!$orientation)
			$orientation = $this->DefOrientation;
		else {
			$orientation = strtoupper(substr($orientation, 0, 1));
			if ($orientation != $this->DefOrientation)
				$this->OrientationChanges[$this->page] = true;
		}
		if ($orientation != $this->CurOrientation || $newformat) {

			//Change orientation
			if ($orientation == 'P') {
				$this->wPt = $this->fwPt;
				$this->hPt = $this->fhPt;
				$this->w = $this->fw;
				$this->h = $this->fh;
				if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation == 'P') {
					$this->tMargin = $this->orig_tMargin;
					$this->bMargin = $this->orig_bMargin;
					$this->DeflMargin = $this->orig_lMargin;
					$this->DefrMargin = $this->orig_rMargin;
					$this->margin_header = $this->orig_hMargin;
					$this->margin_footer = $this->orig_fMargin;
				} else {
					$resetHTMLHeadersrequired = true;
				}
			} else {
				$this->wPt = $this->fhPt;
				$this->hPt = $this->fwPt;
				$this->w = $this->fh;
				$this->h = $this->fw;
				if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation == 'P') {
					$this->tMargin = $this->orig_lMargin;
					$this->bMargin = $this->orig_rMargin;
					$this->DeflMargin = $this->orig_bMargin;
					$this->DefrMargin = $this->orig_tMargin;
					$this->margin_header = $this->orig_hMargin;
					$this->margin_footer = $this->orig_fMargin;
				} else {
					$resetHTMLHeadersrequired = true;
				}
			}
			$this->CurOrientation = $orientation;
			$this->ResetMargins();
			$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
			$this->PageBreakTrigger = $this->h - $this->bMargin;
		}

		$this->pageDim[$this->page]['w'] = $this->w;
		$this->pageDim[$this->page]['h'] = $this->h;

		$this->pageDim[$this->page]['outer_width_LR'] = isset($this->page_box['outer_width_LR']) ? $this->page_box['outer_width_LR'] : 0;
		$this->pageDim[$this->page]['outer_width_TB'] = isset($this->page_box['outer_width_TB']) ? $this->page_box['outer_width_TB'] : 0;
		if (!isset($this->page_box['outer_width_LR']) && !isset($this->page_box['outer_width_TB'])) {
			$this->pageDim[$this->page]['bleedMargin'] = 0;
		} else if ($this->bleedMargin <= $this->page_box['outer_width_LR'] && $this->bleedMargin <= $this->page_box['outer_width_TB']) {
			$this->pageDim[$this->page]['bleedMargin'] = $this->bleedMargin;
		} else {
			$this->pageDim[$this->page]['bleedMargin'] = min($this->page_box['outer_width_LR'], $this->page_box['outer_width_TB']) - 0.01;
		}

		// If Page Margins are re-defined
		// strlen()>0 is used to pick up (integer) 0, (string) '0', or set value
		if ((strlen($mgl) > 0 && $this->DeflMargin != $mgl) || (strlen($mgr) > 0 && $this->DefrMargin != $mgr) || (strlen($mgt) > 0 && $this->tMargin != $mgt) || (strlen($mgb) > 0 && $this->bMargin != $mgb) || (strlen($mgh) > 0 && $this->margin_header != $mgh) || (strlen($mgf) > 0 && $this->margin_footer != $mgf)) {
			if (strlen($mgl) > 0)
				$this->DeflMargin = $mgl;
			if (strlen($mgr) > 0)
				$this->DefrMargin = $mgr;
			if (strlen($mgt) > 0)
				$this->tMargin = $mgt;
			if (strlen($mgb) > 0)
				$this->bMargin = $mgb;
			if (strlen($mgh) > 0)
				$this->margin_header = $mgh;
			if (strlen($mgf) > 0)
				$this->margin_footer = $mgf;
			$this->ResetMargins();
			$this->SetAutoPageBreak($this->autoPageBreak, $this->bMargin);
			$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
			$resetHTMLHeadersrequired = true;
		}

		$this->ResetMargins();
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
		$this->SetAutoPageBreak($this->autoPageBreak, $this->bMargin);

		// Reset column top margin
		$this->y0 = $this->tMargin;

		$this->x = $this->lMargin;
		$this->y = $this->tMargin;
		$this->FontFamily = '';

		// HEADERS AND FOOTERS	// mPDF 6
		if ($ohvalue < 0 || strtoupper($ohvalue) == 'OFF') {
			$this->HTMLHeader = '';
			$resetHTMLHeadersrequired = true;
		} else if ($ohname && $ohvalue > 0) {
			if (preg_match('/^html_(.*)$/i', $ohname, $n)) {
				$name = $n[1];
			} else {
				$name = $ohname;
			}
			if (isset($this->pageHTMLheaders[$name])) {
				$this->HTMLHeader = $this->pageHTMLheaders[$name];
			} else {
				$this->HTMLHeader = '';
			}
			$resetHTMLHeadersrequired = true;
		}

		if ($ehvalue < 0 || strtoupper($ehvalue) == 'OFF') {
			$this->HTMLHeaderE = '';
			$resetHTMLHeadersrequired = true;
		} else if ($ehname && $ehvalue > 0) {
			if (preg_match('/^html_(.*)$/i', $ehname, $n)) {
				$name = $n[1];
			} else {
				$name = $ehname;
			}
			if (isset($this->pageHTMLheaders[$name])) {
				$this->HTMLHeaderE = $this->pageHTMLheaders[$name];
			} else {
				$this->HTMLHeaderE = '';
			}
			$resetHTMLHeadersrequired = true;
		}

		if ($ofvalue < 0 || strtoupper($ofvalue) == 'OFF') {
			$this->HTMLFooter = '';
			$resetHTMLHeadersrequired = true;
		} else if ($ofname && $ofvalue > 0) {
			if (preg_match('/^html_(.*)$/i', $ofname, $n)) {
				$name = $n[1];
			} else {
				$name = $ofname;
			}
			if (isset($this->pageHTMLfooters[$name])) {
				$this->HTMLFooter = $this->pageHTMLfooters[$name];
			} else {
				$this->HTMLFooter = '';
			}
			$resetHTMLHeadersrequired = true;
		}

		if ($efvalue < 0 || strtoupper($efvalue) == 'OFF') {
			$this->HTMLFooterE = '';
			$resetHTMLHeadersrequired = true;
		} else if ($efname && $efvalue > 0) {
			if (preg_match('/^html_(.*)$/i', $efname, $n)) {
				$name = $n[1];
			} else {
				$name = $efname;
			}
			if (isset($this->pageHTMLfooters[$name])) {
				$this->HTMLFooterE = $this->pageHTMLfooters[$name];
			} else {
				$this->HTMLFooterE = '';
			}
			$resetHTMLHeadersrequired = true;
		}
		if ($resetHTMLHeadersrequired) {
			$this->SetHTMLHeader($this->HTMLHeader);
			$this->SetHTMLHeader($this->HTMLHeaderE, 'E');
			$this->SetHTMLFooter($this->HTMLFooter);
			$this->SetHTMLFooter($this->HTMLFooterE, 'E');
		}


		if (($this->mirrorMargins) && (($this->page) % 2 == 0)) { // EVEN
			$this->_setAutoHeaderHeight($this->HTMLHeaderE);
			$this->_setAutoFooterHeight($this->HTMLFooterE);
		} else { // ODD or DEFAULT
			$this->_setAutoHeaderHeight($this->HTMLHeader);
			$this->_setAutoFooterHeight($this->HTMLFooter);
		}
		// Reset column top margin
		$this->y0 = $this->tMargin;

		$this->x = $this->lMargin;
		$this->y = $this->tMargin;
	}

// mPDF 6
	function _setAutoHeaderHeight(&$htmlh)
	{
		if ($this->setAutoTopMargin == 'pad') {
			if (isset($htmlh['h']) && $htmlh['h']) {
				$h = $htmlh['h'];
			} // 5.7.3
			else {
				$h = 0;
			}
			$this->tMargin = $this->margin_header + $h + $this->orig_tMargin;
		} else if ($this->setAutoTopMargin == 'stretch') {
			if (isset($htmlh['h']) && $htmlh['h']) {
				$h = $htmlh['h'];
			} // 5.7.3
			else {
				$h = 0;
			}
			$this->tMargin = max($this->orig_tMargin, $this->margin_header + $h + $this->autoMarginPadding);
		}
	}

// mPDF 6
	function _setAutoFooterHeight(&$htmlf)
	{
		if ($this->setAutoBottomMargin == 'pad') {
			if (isset($htmlf['h']) && $htmlf['h']) {
				$h = $htmlf['h'];
			} // 5.7.3
			else {
				$h = 0;
			}
			$this->bMargin = $this->margin_footer + $h + $this->orig_bMargin;
			$this->PageBreakTrigger = $this->h - $this->bMargin;
		} else if ($this->setAutoBottomMargin == 'stretch') {
			if (isset($htmlf['h']) && $htmlf['h']) {
				$h = $htmlf['h'];
			} // 5.7.3
			else {
				$h = 0;
			}
			$this->bMargin = max($this->orig_bMargin, $this->margin_footer + $h + $this->autoMarginPadding);
			$this->PageBreakTrigger = $this->h - $this->bMargin;
		}
	}

	function _endpage()
	{
		/* -- CSS-IMAGE-FLOAT -- */
		$this->printfloatbuffer();
		/* -- END CSS-IMAGE-FLOAT -- */

		if ($this->visibility != 'visible')
			$this->SetVisibility('visible');
		$this->EndLayer();
		//End of page contents
		$this->state = 1;
	}

	function _newobj($obj_id = false, $onlynewobj = false)
	{
		if (!$obj_id) {
			$obj_id = ++$this->n;
		}
		//Begin a new object
		if (!$onlynewobj) {
			$this->offsets[$obj_id] = strlen($this->buffer);
			$this->_out($obj_id . ' 0 obj');
			$this->_current_obj_id = $obj_id; // for later use with encryption
		}
	}

	function _dounderline($x, $y, $txt, $OTLdata = false, $textvar = 0)
	{
		// Now print line exactly where $y secifies - called from Text() and Cell() - adjust  position there
		// WORD SPACING
		$w = ($this->GetStringWidth($txt, false, $OTLdata, $textvar) * _MPDFK) + ($this->charspacing * mb_strlen($txt, $this->mb_enc)) + ( $this->ws * mb_substr_count($txt, ' ', $this->mb_enc));
		//Draw a line
		return sprintf('%.3F %.3F m %.3F %.3F l S', $x * _MPDFK, ($this->h - $y) * _MPDFK, ($x * _MPDFK) + $w, ($this->h - $y) * _MPDFK);
	}

	


//==============================================================


	function _trnsvalue($s, $bpc)
	{
		// Corrects 2-byte integer to 8-bit depth value
		// If original image is bpc != 8, tRNS will be in this bpc
		// $im from imagecreatefromstring will always be in bpc=8
		// So why do we only need to correct 16-bit tRNS and NOT 2 or 4-bit???
		$n = $this->_twobytes2int($s);
		if ($bpc == 16) {
			$n = ($n >> 8);
		}
		//else if ($bpc==4) { $n = ($n << 2); }
		//else if ($bpc==2) { $n = ($n << 4); }
		return $n;
	}

	function _fourbytes2int($s)
	{
		//Read a 4-byte integer from string
		return (ord($s[0]) << 24) + (ord($s[1]) << 16) + (ord($s[2]) << 8) + ord($s[3]);
	}

	function _twobytes2int($s)
	{ // equivalent to _get_ushort
		//Read a 2-byte integer from string
		return (ord(substr($s, 0, 1)) << 8) + ord(substr($s, 1, 1));
	}

	function _jpgHeaderFromString(&$data)
	{
		$p = 4;
		$p += $this->_twobytes2int(substr($data, $p, 2)); // Length of initial marker block
		$marker = substr($data, $p, 2);
		while ($marker != chr(255) . chr(192) && $marker != chr(255) . chr(194) && $p < strlen($data)) {
			// Start of frame marker (FFC0) or (FFC2) mPDF 4.4.004
			$p += ($this->_twobytes2int(substr($data, $p + 2, 2))) + 2; // Length of marker block
			$marker = substr($data, $p, 2);
		}
		if ($marker != chr(255) . chr(192) && $marker != chr(255) . chr(194)) {
			return false;
		}
		return substr($data, $p + 2, 10);
	}

	function _jpgDataFromHeader($hdr)
	{
		$bpc = ord(substr($hdr, 2, 1));
		if (!$bpc) {
			$bpc = 8;
		}
		$h = $this->_twobytes2int(substr($hdr, 3, 2));
		$w = $this->_twobytes2int(substr($hdr, 5, 2));
		$channels = ord(substr($hdr, 7, 1));
		if ($channels == 3) {
			$colspace = 'DeviceRGB';
		} elseif ($channels == 4) {
			$colspace = 'DeviceCMYK';
		} else {
			$colspace = 'DeviceGray';
		}
		return array($w, $h, $colspace, $bpc, $channels);
	}

	function file_get_contents_by_curl($url, &$data)
	{
		$timeout = 5;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1'); // mPDF 5.7.4
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
	}

	function file_get_contents_by_socket($url, &$data)
	{
		// mPDF 5.7.3
		$timeout = 1;
		$p = parse_url($url);
		$file = $p['path'];
		if ($p['scheme'] == 'https') {
			$prefix = 'ssl://';
			$port = ($p['port'] ? $p['port'] : 443);
		} else {
			$prefix = '';
			$port = ($p['port'] ? $p['port'] : 80);
		}
		if ($p['query']) {
			$file .= '?' . $p['query'];
		}
		if (!($fh = @fsockopen($prefix . $p['host'], $port, $errno, $errstr, $timeout))) {
			return false;
		}

		$getstring = "GET " . $file . " HTTP/1.0 \r\n" .
			"Host: " . $p['host'] . " \r\n" .
			"Connection: close\r\n\r\n";
		fwrite($fh, $getstring);
		// Get rid of HTTP header
		$s = fgets($fh, 1024);
		if (!$s) {
			return false;
		}
		$httpheader .= $s;
		while (!feof($fh)) {
			$s = fgets($fh, 1024);
			if ($s == "\r\n") {
				break;
			}
		}
		$data = '';
		while (!feof($fh)) {
			$data .= fgets($fh, 1024);
		}
		fclose($fh);
	}

//==============================================================

	function _imageTypeFromString(&$data)
	{
		$type = '';
		if (substr($data, 6, 4) == 'JFIF' || substr($data, 6, 4) == 'Exif' || substr($data, 0, 2) == chr(255) . chr(216)) { // 0xFF 0xD8	// mpDF 5.7.2
			$type = 'jpeg';
		} else if (substr($data, 0, 6) == "GIF87a" || substr($data, 0, 6) == "GIF89a") {
			$type = 'gif';
		} else if (substr($data, 0, 8) == chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10)) {
			$type = 'png';
		}
		/* -- IMAGES-WMF -- */ else if (substr($data, 0, 4) == chr(215) . chr(205) . chr(198) . chr(154)) {
			$type = 'wmf';
		}
		/* -- END IMAGES-WMF -- */ else if (preg_match('/<svg.*<\/svg>/is', $data)) {
			$type = 'svg';
		}
		// BMP images
		else if (substr($data, 0, 2) == "BM") {
			$type = 'bmp';
		}
		return $type;
	}

//==============================================================
// Moved outside WMF as also needed for SVG
	function _putformobjects()
	{
		reset($this->formobjects);
		while (list($file, $info) = each($this->formobjects)) {
			$this->_newobj();
			$this->formobjects[$file]['n'] = $this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Form');
			$this->_out('/Group ' . ($this->n + 1) . ' 0 R');
			$this->_out('/BBox [' . $info['x'] . ' ' . $info['y'] . ' ' . ($info['w'] + $info['x']) . ' ' . ($info['h'] + $info['y']) . ']');
			if ($this->compress)
				$this->_out('/Filter /FlateDecode');
			$data = ($this->compress) ? gzcompress($info['data']) : $info['data'];
			$this->_out('/Length ' . strlen($data) . '>>');
			$this->_putstream($data);
			unset($this->formobjects[$file]['data']);
			$this->_out('endobj');
			// Required for SVG transparency (opacity) to work
			$this->_newobj();
			$this->_out('<</Type /Group');
			$this->_out('/S /Transparency');
			$this->_out('>>');
			$this->_out('endobj');
		}
	}

	function _freadint($f)
	{
		//Read a 4-byte integer from file
		$i = ord(fread($f, 1)) << 24;
		$i+=ord(fread($f, 1)) << 16;
		$i+=ord(fread($f, 1)) << 8;
		$i+=ord(fread($f, 1));
		return $i;
	}

	function _UTF16BEtextstring($s)
	{
		$s = $this->UTF8ToUTF16BE($s, true);
		/* -- ENCRYPTION -- */
		if ($this->encrypted) {
			$s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
		}
		/* -- END ENCRYPTION -- */
		return '(' . $this->_escape($s) . ')';
	}

	function _textstring($s)
	{
		/* -- ENCRYPTION -- */
		if ($this->encrypted) {
			$s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
		}
		/* -- END ENCRYPTION -- */
		return '(' . $this->_escape($s) . ')';
	}

	function _escape($s)
	{
		// the chr(13) substitution fixes the Bugs item #1421290.
		return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r'));
	}

	function _putstream($s)
	{
		/* -- ENCRYPTION -- */
		if ($this->encrypted) {
			$s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
		}
		/* -- END ENCRYPTION -- */
		$this->_out('stream');
		$this->_out($s);
		$this->_out('endstream');
	}

	function _out($s, $ln = true)
	{
		if ($this->state == 2) {
			if ($this->bufferoutput) {
				$this->headerbuffer.= $s . "\n";
			}
			/* -- COLUMNS -- */ else if (($this->ColActive) && !$this->processingHeader && !$this->processingFooter) {
				// Captures everything in buffer for columns; Almost everything is sent from fn. Cell() except:
				// Images sent from Image() or
				// later sent as _out($textto) in printbuffer
				// Line()
				if (preg_match('/q \d+\.\d\d+ 0 0 (\d+\.\d\d+) \d+\.\d\d+ \d+\.\d\d+ cm \/(I|FO)\d+ Do Q/', $s, $m)) { // Image data
					$h = ($m[1] / _MPDFK);
					// Update/overwrite the lowest bottom of printing y value for a column
					$this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y + $h;
				}
				/* -- TABLES -- */ else if (preg_match('/\d+\.\d\d+ \d+\.\d\d+ \d+\.\d\d+ ([\-]{0,1}\d+\.\d\d+) re/', $s, $m) && $this->tableLevel > 0) { // Rect in table
					$h = ($m[1] / _MPDFK);
					// Update/overwrite the lowest bottom of printing y value for a column
					$this->ColDetails[$this->CurrCol]['bottom_margin'] = max($this->ColDetails[$this->CurrCol]['bottom_margin'], ($this->y + $h));
				}
				/* -- END TABLES -- */ else {  // Td Text Set in Cell()
					if (isset($this->ColDetails[$this->CurrCol]['bottom_margin'])) {
						$h = $this->ColDetails[$this->CurrCol]['bottom_margin'] - $this->y;
					} else {
						$h = 0;
					}
				}
				if ($h < 0) {
					$h = -$h;
				}
				$this->columnbuffer[] = array(
					's' => $s, // Text string to output
					'col' => $this->CurrCol, // Column when printed
					'x' => $this->x, // x when printed
					'y' => $this->y, // this->y when printed (after column break)
					'h' => $h        // actual y at bottom when printed = y+h
				);
			}
			/* -- END COLUMNS -- */
			/* -- TABLES -- */ else if ($this->table_rotate && !$this->processingHeader && !$this->processingFooter) {
				// Captures eveything in buffer for rotated tables;
				$this->tablebuffer .= $s . "\n";
			}
			/* -- END TABLES -- */ else if ($this->kwt && !$this->processingHeader && !$this->processingFooter) {
				// Captures eveything in buffer for keep-with-table (h1-6);
				$this->kwt_buffer[] = array(
					's' => $s, // Text string to output
					'x' => $this->x, // x when printed
					'y' => $this->y, // y when printed
				);
			} else if (($this->keep_block_together) && !$this->processingHeader && !$this->processingFooter) {
				// do nothing
			} else {
				$this->pages[$this->page] .= $s . ($ln == true ? "\n" : '');
			}
		} else {
			$this->buffer .= $s . ($ln == true ? "\n" : '');
		}
	}

	


//====================================================


	/* -- END DIRECTW -- */

	function UTF8StringToArray($str, $addSubset = true)
	{
		$out = array();
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$uni = -1;
			$h = ord($str[$i]);
			if ($h <= 0x7F)
				$uni = $h;
			elseif ($h >= 0xC2) {
				if (($h <= 0xDF) && ($i < $len - 1))
					$uni = ($h & 0x1F) << 6 | (ord($str[++$i]) & 0x3F);
				elseif (($h <= 0xEF) && ($i < $len - 2))
					$uni = ($h & 0x0F) << 12 | (ord($str[++$i]) & 0x3F) << 6 | (ord($str[++$i]) & 0x3F);
				elseif (($h <= 0xF4) && ($i < $len - 3))
					$uni = ($h & 0x0F) << 18 | (ord($str[++$i]) & 0x3F) << 12 | (ord($str[++$i]) & 0x3F) << 6 | (ord($str[++$i]) & 0x3F);
			}
			if ($uni >= 0) {
				$out[] = $uni;
				if ($addSubset && isset($this->CurrentFont['subset'])) {
					$this->CurrentFont['subset'][$uni] = $uni;
				}
			}
		}
		return $out;
	}

//Convert utf-8 string to <HHHHHH> for Font Subsets
	function UTF8toSubset($str)
	{
		$ret = '<';
		//$str = preg_replace('/'.preg_quote($this->aliasNbPg,'/').'/', chr(7), $str );	// mPDF 6 deleted
		//$str = preg_replace('/'.preg_quote($this->aliasNbPgGp,'/').'/', chr(8), $str );	// mPDF 6 deleted
		$unicode = $this->UTF8StringToArray($str);
		$orig_fid = $this->CurrentFont['subsetfontids'][0];
		$last_fid = $this->CurrentFont['subsetfontids'][0];
		foreach ($unicode as $c) {
			/* 	// mPDF 6 deleted
			  if ($c == 7 || $c == 8) {
			  if ($orig_fid != $last_fid) {
			  $ret .= '> Tj /F'.$orig_fid.' '.$this->FontSizePt.' Tf <';
			  $last_fid = $orig_fid;
			  }
			  if ($c == 7) { $ret .= $this->aliasNbPgHex; }
			  else { $ret .= $this->aliasNbPgGpHex; }
			  continue;
			  }
			 */
			if (!$this->_charDefined($this->CurrentFont['cw'], $c)) {
				$c = 0;
			} // mPDF 6
			for ($i = 0; $i < 99; $i++) {
				// return c as decimal char
				$init = array_search($c, $this->CurrentFont['subsets'][$i]);
				if ($init !== false) {
					if ($this->CurrentFont['subsetfontids'][$i] != $last_fid) {
						$ret .= '> Tj /F' . $this->CurrentFont['subsetfontids'][$i] . ' ' . $this->FontSizePt . ' Tf <';
						$last_fid = $this->CurrentFont['subsetfontids'][$i];
					}
					$ret .= sprintf("%02s", strtoupper(dechex($init)));
					break;
				}
				// TrueType embedded SUBSETS
				else if (count($this->CurrentFont['subsets'][$i]) < 255) {
					$n = count($this->CurrentFont['subsets'][$i]);
					$this->CurrentFont['subsets'][$i][$n] = $c;
					if ($this->CurrentFont['subsetfontids'][$i] != $last_fid) {
						$ret .= '> Tj /F' . $this->CurrentFont['subsetfontids'][$i] . ' ' . $this->FontSizePt . ' Tf <';
						$last_fid = $this->CurrentFont['subsetfontids'][$i];
					}
					$ret .= sprintf("%02s", strtoupper(dechex($n)));
					break;
				} else if (!isset($this->CurrentFont['subsets'][($i + 1)])) {
					// TrueType embedded SUBSETS
					$this->CurrentFont['subsets'][($i + 1)] = array(0 => 0);
					$new_fid = count($this->fonts) + $this->extraFontSubsets + 1;
					$this->CurrentFont['subsetfontids'][($i + 1)] = $new_fid;
					$this->extraFontSubsets++;
				}
			}
		}
		$ret .= '>';
		if ($last_fid != $orig_fid) {
			$ret .= ' Tj /F' . $orig_fid . ' ' . $this->FontSizePt . ' Tf <> ';
		}
		return $ret;
	}

// Converts UTF-8 strings to UTF16-BE.
	function UTF8ToUTF16BE($str, $setbom = true)
	{
		if ($this->checkSIP && preg_match("/([\x{20000}-\x{2FFFF}])/u", $str)) {
			if (!in_array($this->currentfontfamily, array('gb', 'big5', 'sjis', 'uhc', 'gbB', 'big5B', 'sjisB', 'uhcB', 'gbI', 'big5I', 'sjisI', 'uhcI',
					'gbBI', 'big5BI', 'sjisBI', 'uhcBI'))) {
				$str = preg_replace("/[\x{20000}-\x{2FFFF}]/u", chr(0), $str);
			}
		}
		if ($this->checkSMP && preg_match("/([\x{10000}-\x{1FFFF}])/u", $str)) {
			$str = preg_replace("/[\x{10000}-\x{1FFFF}]/u", chr(0), $str);
		}
		$outstr = ""; // string to be returned
		if ($setbom) {
			$outstr .= "\xFE\xFF"; // Byte Order Mark (BOM)
		}
		$outstr .= mb_convert_encoding($str, 'UTF-16BE', 'UTF-8');
		return $outstr;
	}

// ====================================================
// ====================================================


//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

	function SetDefaultFont($font)
	{
		// Disallow embedded fonts to be used as defaults in PDFA
		if ($this->PDFA || $this->PDFX) {
			if (strtolower($font) == 'ctimes') {
				$font = 'serif';
			}
			if (strtolower($font) == 'ccourier') {
				$font = 'monospace';
			}
			if (strtolower($font) == 'chelvetica') {
				$font = 'sans-serif';
			}
		}
		$font = $this->SetFont($font); // returns substituted font if necessary
		$this->default_font = $font;
		$this->original_default_font = $font;
		if (!$this->watermark_font) {
			$this->watermark_font = $font;
		} // *WATERMARK*
		$this->defaultCSS['BODY']['FONT-FAMILY'] = $font;
		$this->cssmgr->CSS['BODY']['FONT-FAMILY'] = $font;
	}

	function SetDefaultFontSize($fontsize)
	{
		$this->default_font_size = $fontsize;
		$this->original_default_font_size = $fontsize;
		$this->SetFontSize($fontsize);
		$this->defaultCSS['BODY']['FONT-SIZE'] = $fontsize . 'pt';
		$this->cssmgr->CSS['BODY']['FONT-SIZE'] = $fontsize . 'pt';
	}

	function SetDefaultBodyCSS($prop, $val)
	{
		if ($prop) {
			$this->defaultCSS['BODY'][strtoupper($prop)] = $val;
			$this->cssmgr->CSS['BODY'][strtoupper($prop)] = $val;
		}
	}

	function SetDirectionality($dir = 'ltr')
	{
		/* -- OTL -- */
		if (strtolower($dir) == 'rtl') {
			if ($this->directionality != 'rtl') {
				// Swop L/R Margins so page 1 RTL is an 'even' page
				$tmp = $this->DeflMargin;
				$this->DeflMargin = $this->DefrMargin;
				$this->DefrMargin = $tmp;
				$this->orig_lMargin = $this->DeflMargin;
				$this->orig_rMargin = $this->DefrMargin;

				$this->SetMargins($this->DeflMargin, $this->DefrMargin, $this->tMargin);
			}
			$this->directionality = 'rtl';
			$this->defaultAlign = 'R';
			$this->defaultTableAlign = 'R';
		} else {
			/* -- END OTL -- */
			$this->directionality = 'ltr';
			$this->defaultAlign = 'L';
			$this->defaultTableAlign = 'L';
		} // *OTL*
		$this->cssmgr->CSS['BODY']['DIRECTION'] = $this->directionality;
	}

// Return either a number (factor) - based on current set fontsize (if % or em) - or exact lineheight (with 'mm' after it)
	function fixLineheight($v)
	{
		$lh = false;
		if (preg_match('/^[0-9\.,]*$/', $v) && $v >= 0) {
			return ($v + 0);
		} else if (strtoupper($v) == 'NORMAL' || $v == 'N') {
			return 'N';  // mPDF 6
		} else {
			$tlh = $this->ConvertSize($v, $this->FontSize, $this->FontSize, true);
			if ($tlh) {
				return ($tlh . 'mm');
			}
		}
		return $this->normalLineheight;
	}

	function _getNormalLineheight($desc = false)
	{
		if (!$desc) {
			$desc = $this->CurrentFont['desc'];
		}
		if ($this->useFixedNormalLineHeight) {
			$lh = $this->normalLineheight;
		} else if (isset($desc['Ascent']) && $desc['Ascent']) {
			$lh = ($this->adjustFontDescLineheight * ($desc['Ascent'] - $desc['Descent'] + $desc['Leading']) / 1000);
		} else {
			$lh = $this->normalLineheight;
		}
		return $lh;
	}

// Set a (fixed) lineheight to an actual value - either to named fontsize(pts) or default
	function SetLineHeight($FontPt = '', $lh = '')
	{
		if (!$FontPt) {
			$FontPt = $this->FontSizePt;
		}
		$fs = $FontPt / _MPDFK;
		$this->lineheight = $this->_computeLineheight($lh, $fs);
	}

	function _computeLineheight($lh, $fs = '')
	{
		if ($this->shrin_k > 1) {
			$k = $this->shrin_k;
		} else {
			$k = 1;
		}
		if (!$fs) {
			$fs = $this->FontSize;
		}
		if ($lh == 'N') {
			$lh = $this->_getNormalLineheight();
		}
		if (preg_match('/mm/', $lh)) {
			return (($lh + 0.0) / $k); // convert to number
		} else if ($lh > 0) {
			return ($fs * $lh);
		}
		return ($fs * $this->normalLineheight);
	}

	function _setLineYpos(&$fontsize, &$fontdesc, &$CSSlineheight, $blockYpos = false)
	{
		$ypos['glyphYorigin'] = 0;
		$ypos['baseline-shift'] = 0;
		$linegap = 0;
		$leading = 0;

		if (isset($fontdesc['Ascent']) && $fontdesc['Ascent'] && !$this->useFixedTextBaseline) {
			// Fontsize uses font metrics - this method seems to produce results compatible with browsers (except IE9)
			$ypos['boxtop'] = $fontdesc['Ascent'] / 1000 * $fontsize;
			$ypos['boxbottom'] = $fontdesc['Descent'] / 1000 * $fontsize;
			if (isset($fontdesc['Leading'])) {
				$linegap = $fontdesc['Leading'] / 1000 * $fontsize;
			}
		}
		// Default if not set - uses baselineC
		else {
			$ypos['boxtop'] = (0.5 + $this->baselineC) * $fontsize;
			$ypos['boxbottom'] = -(0.5 - $this->baselineC) * $fontsize;
		}
		$fontheight = $ypos['boxtop'] - $ypos['boxbottom'];

		if ($this->shrin_k > 1) {
			$shrin_k = $this->shrin_k;
		} else {
			$shrin_k = 1;
		}

		$leading = 0;
		if ($CSSlineheight == 'N') {
			$lh = $this->_getNormalLineheight($fontdesc);
			$lineheight = ($fontsize * $lh);
			$leading += $linegap; // specified in hhea or sTypo in OpenType tables	****************************************
		} else if (preg_match('/mm/', $CSSlineheight)) {
			$lineheight = (($CSSlineheight + 0.0) / $shrin_k);
		} // convert to number
		// ??? If lineheight is a factor e.g. 1.3  ?? use factor x 1em or ? use 'normal' lineheight * factor ******************************
		// Could depend on value for $text_height - a draft CSS value as set above for now
		else if ($CSSlineheight > 0) {
			$lineheight = ($fontsize * $CSSlineheight);
		} else {
			$lineheight = ($fontsize * $this->normalLineheight);
		}

		// In general, calculate the "leading" - the difference between the fontheight and the lineheight
		// and add half to the top and half to the bottom. BUT
		// If an inline element has a font-size less than the block element, and the line-height is set as an em or % value
		// it will add too much leading below the font and expand the height of the line - so just use the block element exttop/extbottom:
		if (preg_match('/mm/', $CSSlineheight) && $ypos['boxtop'] < $blockYpos['boxtop'] && $ypos['boxbottom'] > $blockYpos['boxbottom']) {
			$ypos['exttop'] = $blockYpos['exttop'];
			$ypos['extbottom'] = $blockYpos['extbottom'];
		} else {
			$leading += ($lineheight - $fontheight);

			$ypos['exttop'] = $ypos['boxtop'] + $leading / 2;
			$ypos['extbottom'] = $ypos['boxbottom'] - $leading / 2;
		}


		// TEMP ONLY FOR DEBUGGING *********************************
		//$ypos['lineheight'] = $lineheight;
		//$ypos['fontheight'] = $fontheight;
		//$ypos['leading'] = $leading;

		return $ypos;
	}

	/* Called from WriteFlowingBlock() and finishFlowingBlock()
	  Determines the line hieght and glyph/writing position
	  for each element in the line to be written */

	function _setInlineBlockHeights(&$lineBox, &$stackHeight, &$content, &$font, $is_table)
	{
		if ($this->shrin_k > 1) {
			$shrin_k = $this->shrin_k;
		} else {
			$shrin_k = 1;
		}

		$ypos = array();
		$bordypos = array();
		$bgypos = array();

		if ($is_table) {
			// FOR TABLE
			$fontsize = $this->FontSize;
			$fontkey = $this->FontFamily . $this->FontStyle;
			$fontdesc = $this->fonts[$fontkey]['desc'];
			$CSSlineheight = $this->cellLineHeight;
			$line_stacking_strategy = $this->cellLineStackingStrategy; // inline-line-height [default] | block-line-height | max-height | grid-height
			$line_stacking_shift = $this->cellLineStackingShift;  // consider-shifts [default] | disregard-shifts
		} else {
			// FOR BLOCK FONT
			$fontsize = $this->blk[$this->blklvl]['InlineProperties']['size'];
			$fontkey = $this->blk[$this->blklvl]['InlineProperties']['family'] . $this->blk[$this->blklvl]['InlineProperties']['style'];
			$fontdesc = $this->fonts[$fontkey]['desc'];
			$CSSlineheight = $this->blk[$this->blklvl]['line_height'];
			// inline-line-height | block-line-height | max-height | grid-height
			$line_stacking_strategy = (isset($this->blk[$this->blklvl]['line_stacking_strategy']) ? $this->blk[$this->blklvl]['line_stacking_strategy'] : 'inline-line-height');
			// consider-shifts | disregard-shifts
			$line_stacking_shift = (isset($this->blk[$this->blklvl]['line_stacking_shift']) ? $this->blk[$this->blklvl]['line_stacking_shift'] : 'consider-shifts');
		}
		$boxLineHeight = $this->_computeLineheight($CSSlineheight, $fontsize);


		// First, set a "strut" using block font at index $lineBox[-1]
		$ypos[-1] = $this->_setLineYpos($fontsize, $fontdesc, $CSSlineheight);

		// for the block element - always taking the block EXTENDED progression including leading - which may be negative
		if ($line_stacking_strategy == 'block-line-height') {
			$topy = $ypos[-1]['exttop'];
			$bottomy = $ypos[-1]['extbottom'];
		} else {
			$topy = 0;
			$bottomy = 0;
		}

		// Get text-middle for aligning images/objects
		$midpoint = $ypos[-1]['boxtop'] - (($ypos[-1]['boxtop'] - $ypos[-1]['boxbottom']) / 2);

		// for images / inline objects / replaced elements
		$mta = 0; // Maximum top-aligned
		$mba = 0; // Maximum bottom-aligned
		foreach ($content as $k => $chunk) {
			if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]['type'] == 'listmarker') {
				$ypos[$k] = $ypos[-1];
				// UPDATE Maximums
				if ($line_stacking_strategy == 'block-line-height' || $line_stacking_strategy == 'grid-height' || $line_stacking_strategy == 'max-height') { // don't include extended block progression of all inline elements
					if ($ypos[$k]['boxtop'] > $topy)
						$topy = $ypos[$k]['boxtop'];
					if ($ypos[$k]['boxbottom'] < $bottomy)
						$bottomy = $ypos[$k]['boxbottom'];
				}
				else {
					if ($ypos[$k]['exttop'] > $topy)
						$topy = $ypos[$k]['exttop'];
					if ($ypos[$k]['extbottom'] < $bottomy)
						$bottomy = $ypos[$k]['extbottom'];
				}
			}
			else if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]['type'] == 'dottab') { // mPDF 6 DOTTAB
				$fontsize = $font[$k]['size'];
				$fontdesc = $font[$k]['curr']['desc'];
				$lh = 1;
				$ypos[$k] = $this->_setLineYpos($fontsize, $fontdesc, $lh, $ypos[-1]); // Lineheight=1 fixed
			} else if (isset($this->objectbuffer[$k])) {
				$oh = $this->objectbuffer[$k]['OUTER-HEIGHT'];
				$va = $this->objectbuffer[$k]['vertical-align'];

				if ($va == 'BS') { //  (BASELINE default)
					if ($oh > $topy)
						$topy = $oh;
				}
				else if ($va == 'M') {
					if (($midpoint + $oh / 2) > $topy)
						$topy = $midpoint + $oh / 2;
					if (($midpoint - $oh / 2) < $bottomy)
						$bottomy = $midpoint - $oh / 2;
				}
				else if ($va == 'TT') {
					if (($ypos[-1]['boxtop'] - $oh) < $bottomy) {
						$bottomy = $ypos[-1]['boxtop'] - $oh;
						$topy = max($topy, $ypos[-1]['boxtop']);
					}
				} else if ($va == 'TB') {
					if (($ypos[-1]['boxbottom'] + $oh) > $topy) {
						$topy = $ypos[-1]['boxbottom'] + $oh;
						$bottomy = min($bottomy, $ypos[-1]['boxbottom']);
					}
				} else if ($va == 'T') {
					if ($oh > $mta)
						$mta = $oh;
				}
				else if ($va == 'B') {
					if ($oh > $mba)
						$mba = $oh;
				}
			}
			else if ($content[$k] || $content[$k] === '0') {
				// FOR FLOWING BLOCK
				$fontsize = $font[$k]['size'];
				$fontdesc = $font[$k]['curr']['desc'];
				// In future could set CSS line-height from inline elements; for now, use block level:
				$ypos[$k] = $this->_setLineYpos($fontsize, $fontdesc, $CSSlineheight, $ypos[-1]);

				if (isset($font[$k]['textparam']['text-baseline']) && $font[$k]['textparam']['text-baseline'] != 0) {
					$ypos[$k]['baseline-shift'] = $font[$k]['textparam']['text-baseline'];
				}

				// DO ALIGNMENT FOR BASELINES *******************
				// Until most fonts have OpenType BASE tables, this won't work
				// $ypos[$k] compared to $ypos[-1] or $ypos[$k-1] using $dominant_baseline and $baseline_table
				// UPDATE Maximums
				if ($line_stacking_strategy == 'block-line-height' || $line_stacking_strategy == 'grid-height' || $line_stacking_strategy == 'max-height') { // don't include extended block progression of all inline elements
					if ($line_stacking_shift == 'disregard-shifts') {
						if ($ypos[$k]['boxtop'] > $topy)
							$topy = $ypos[$k]['boxtop'];
						if ($ypos[$k]['boxbottom'] < $bottomy)
							$bottomy = $ypos[$k]['boxbottom'];
					}
					else {
						if (($ypos[$k]['boxtop'] + $ypos[$k]['baseline-shift']) > $topy)
							$topy = $ypos[$k]['boxtop'] + $ypos[$k]['baseline-shift'];
						if (($ypos[$k]['boxbottom'] + $ypos[$k]['baseline-shift']) < $bottomy)
							$bottomy = $ypos[$k]['boxbottom'] + $ypos[$k]['baseline-shift'];
					}
				}
				else {
					if ($line_stacking_shift == 'disregard-shifts') {
						if ($ypos[$k]['exttop'] > $topy)
							$topy = $ypos[$k]['exttop'];
						if ($ypos[$k]['extbottom'] < $bottomy)
							$bottomy = $ypos[$k]['extbottom'];
					}
					else {
						if (($ypos[$k]['exttop'] + $ypos[$k]['baseline-shift']) > $topy)
							$topy = $ypos[$k]['exttop'] + $ypos[$k]['baseline-shift'];
						if (($ypos[$k]['extbottom'] + $ypos[$k]['baseline-shift']) < $bottomy)
							$bottomy = $ypos[$k]['extbottom'] + $ypos[$k]['baseline-shift'];
					}
				}

				// If BORDER set on inline element
				if (isset($font[$k]['bord']) && $font[$k]['bord']) {
					$bordfontsize = $font[$k]['textparam']['bord-decoration']['fontsize'] / $shrin_k;
					$bordfontkey = $font[$k]['textparam']['bord-decoration']['fontkey'];
					if ($bordfontkey != $fontkey || $bordfontsize != $fontsize || isset($font[$k]['textparam']['bord-decoration']['baseline'])) {
						$bordfontdesc = $this->fonts[$bordfontkey]['desc'];
						$bordypos[$k] = $this->_setLineYpos($bordfontsize, $bordfontdesc, $CSSlineheight, $ypos[-1]);
						if (isset($font[$k]['textparam']['bord-decoration']['baseline']) && $font[$k]['textparam']['bord-decoration']['baseline'] != 0) {
							$bordypos[$k]['baseline-shift'] = $font[$k]['textparam']['bord-decoration']['baseline'] / $shrin_k;
						}
					}
				}
				// If BACKGROUND set on inline element
				if (isset($font[$k]['spanbgcolor']) && $font[$k]['spanbgcolor']) {
					$bgfontsize = $font[$k]['textparam']['bg-decoration']['fontsize'] / $shrin_k;
					$bgfontkey = $font[$k]['textparam']['bg-decoration']['fontkey'];
					if ($bgfontkey != $fontkey || $bgfontsize != $fontsize || isset($font[$k]['textparam']['bg-decoration']['baseline'])) {
						$bgfontdesc = $this->fonts[$bgfontkey]['desc'];
						$bgypos[$k] = $this->_setLineYpos($bgfontsize, $bgfontdesc, $CSSlineheight, $ypos[-1]);
						if (isset($font[$k]['textparam']['bg-decoration']['baseline']) && $font[$k]['textparam']['bg-decoration']['baseline'] != 0) {
							$bgypos[$k]['baseline-shift'] = $font[$k]['textparam']['bg-decoration']['baseline'] / $shrin_k;
						}
					}
				}
			}
		}


		// TOP or BOTTOM aligned images
		if ($mta > ($topy - $bottomy)) {
			if (($topy - $mta) < $bottomy)
				$bottomy = $topy - $mta;
		}
		if ($mba > ($topy - $bottomy)) {
			if (($bottomy + $mba) > $topy)
				$topy = $bottomy + $mba;
		}

		if ($line_stacking_strategy == 'block-line-height') { // fixed height set by block element (whether present or not)
			$topy = $ypos[-1]['exttop'];
			$bottomy = $ypos[-1]['extbottom'];
		}

		$inclusiveHeight = $topy - $bottomy;

		// SET $stackHeight taking note of line_stacking_strategy
		// NB inclusive height already takes account of need to consider block progression height (excludes leading set by lineheight)
		// or extended block progression height (includes leading set by lineheight)
		if ($line_stacking_strategy == 'block-line-height') { // fixed = extended block progression height of block element
			$stackHeight = $boxLineHeight;
		} else if ($line_stacking_strategy == 'max-height') { // smallest height which includes extended block progression height of block element
			// and block progression heights of inline elements (NOT extended)
			$stackHeight = $inclusiveHeight;
		} else if ($line_stacking_strategy == 'grid-height') { // smallest multiple of block element lineheight to include
			// block progression heights of inline elements (NOT extended)
			$stackHeight = $boxLineHeight;
			while ($stackHeight < $inclusiveHeight) {
				$stackHeight += $boxLineHeight;
			}
		} else { // 'inline-line-height' = default		// smallest height which includes extended block progression height of block element
			// AND extended block progression heights of inline elements
			$stackHeight = $inclusiveHeight;
		}

		$diff = $stackHeight - $inclusiveHeight;
		$topy += $diff / 2;
		$bottomy -= $diff / 2;

		// ADJUST $ypos => lineBox using $stackHeight; lineBox are all offsets from the top of stackHeight in mm
		// and SET IMAGE OFFSETS
		$lineBox[-1]['boxtop'] = $topy - $ypos[-1]['boxtop'];
		$lineBox[-1]['boxbottom'] = $topy - $ypos[-1]['boxbottom'];
//	$lineBox[-1]['exttop'] = $topy - $ypos[-1]['exttop'];
//	$lineBox[-1]['extbottom'] = $topy - $ypos[-1]['extbottom'];
		$lineBox[-1]['glyphYorigin'] = $topy - $ypos[-1]['glyphYorigin'];
		$lineBox[-1]['baseline-shift'] = $ypos[-1]['baseline-shift'];

		$midpoint = $lineBox[-1]['boxbottom'] - (($lineBox[-1]['boxbottom'] - $lineBox[-1]['boxtop']) / 2);

		foreach ($content as $k => $chunk) {
			if (isset($this->objectbuffer[$k])) {
				$oh = $this->objectbuffer[$k]['OUTER-HEIGHT'];
				// LIST MARKERS
				if ($this->objectbuffer[$k]['type'] == 'listmarker') {
					$oh = $fontsize;
				} else if ($this->objectbuffer[$k]['type'] == 'dottab') { // mPDF 6 DOTTAB
					$oh = $font[$k]['size']; // == $this->objectbuffer[$k]['fontsize']/_MPDFK;
					$lineBox[$k]['boxtop'] = $topy - $ypos[$k]['boxtop'];
					$lineBox[$k]['boxbottom'] = $topy - $ypos[$k]['boxbottom'];
					$lineBox[$k]['glyphYorigin'] = $topy - $ypos[$k]['glyphYorigin'];
					$lineBox[$k]['baseline-shift'] = 0;
//				continue;
				}
				$va = $this->objectbuffer[$k]['vertical-align']; // = $objattr['vertical-align'] = set as M,T,B,S

				if ($va == 'BS') { //  (BASELINE default)
					$lineBox[$k]['top'] = $lineBox[-1]['glyphYorigin'] - $oh;
				} else if ($va == 'M') {
					$lineBox[$k]['top'] = $midpoint - $oh / 2;
				} else if ($va == 'TT') {
					$lineBox[$k]['top'] = $lineBox[-1]['boxtop'];
				} else if ($va == 'TB') {
					$lineBox[$k]['top'] = $lineBox[-1]['boxbottom'] - $oh;
				} else if ($va == 'T') {
					$lineBox[$k]['top'] = 0;
				} else if ($va == 'B') {
					$lineBox[$k]['top'] = $stackHeight - $oh;
				}
			} else if ($content[$k] || $content[$k] === '0') {
				$lineBox[$k]['boxtop'] = $topy - $ypos[$k]['boxtop'];
				$lineBox[$k]['boxbottom'] = $topy - $ypos[$k]['boxbottom'];
//			$lineBox[$k]['exttop'] = $topy - $ypos[$k]['exttop'];
//			$lineBox[$k]['extbottom'] = $topy - $ypos[$k]['extbottom'];
				$lineBox[$k]['glyphYorigin'] = $topy - $ypos[$k]['glyphYorigin'];
				$lineBox[$k]['baseline-shift'] = $ypos[$k]['baseline-shift'];
				if (isset($bordypos[$k]['boxtop'])) {
					$lineBox[$k]['border-boxtop'] = $topy - $bordypos[$k]['boxtop'];
					$lineBox[$k]['border-boxbottom'] = $topy - $bordypos[$k]['boxbottom'];
					$lineBox[$k]['border-baseline-shift'] = $bordypos[$k]['baseline-shift'];
				}
				if (isset($bgypos[$k]['boxtop'])) {
					$lineBox[$k]['background-boxtop'] = $topy - $bgypos[$k]['boxtop'];
					$lineBox[$k]['background-boxbottom'] = $topy - $bgypos[$k]['boxbottom'];
					$lineBox[$k]['background-baseline-shift'] = $bgypos[$k]['baseline-shift'];
				}
			}
		}
	}

	function SetBasePath($str = '')
	{
		if (isset($_SERVER['HTTP_HOST'])) {
			$host = $_SERVER['HTTP_HOST'];
		} else if (isset($_SERVER['SERVER_NAME'])) {
			$host = $_SERVER['SERVER_NAME'];
		} else {
			$host = '';
		}
		if (!$str) {
			if ($_SERVER['SCRIPT_NAME']) {
				$currentPath = dirname($_SERVER['SCRIPT_NAME']);
			} else {
				$currentPath = dirname($_SERVER['PHP_SELF']);
			}
			$currentPath = str_replace("\\", "/", $currentPath);
			if ($currentPath == '/') {
				$currentPath = '';
			}
			if ($host) {  // mPDF 6
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
					$currpath = 'https://' . $host . $currentPath . '/';
				} else {
					$currpath = 'http://' . $host . $currentPath . '/';
				}
			} else {
				$currpath = '';
			}
			$this->basepath = $currpath;
			$this->basepathIsLocal = true;
			return;
		}
		$str = preg_replace('/\?.*/', '', $str);
		if (!preg_match('/(http|https|ftp):\/\/.*\//i', $str)) {
			$str .= '/';
		}
		$str .= 'xxx'; // in case $str ends in / e.g. http://www.bbc.co.uk/
		$this->basepath = dirname($str) . "/"; // returns e.g. e.g. http://www.google.com/dir1/dir2/dir3/
		$this->basepath = str_replace("\\", "/", $this->basepath); //If on Windows
		$tr = parse_url($this->basepath);
		if (isset($tr['host']) && ($tr['host'] == $host)) {
			$this->basepathIsLocal = true;
		} else {
			$this->basepathIsLocal = false;
		}
	}

	function GetFullPath(&$path, $basepath = '')
	{
		// When parsing CSS need to pass temporary basepath - so links are relative to current stylesheet
		if (!$basepath) {
			$basepath = $this->basepath;
		}
		//Fix path value
		$path = str_replace("\\", "/", $path); //If on Windows
		// mPDF 5.7.2
		if (substr($path, 0, 2) == "//") {
			$tr = parse_url($basepath);
			$path = $tr['scheme'] . ':' . $path; // mPDF 6
		}

		$regexp = '|^./|'; // Inadvertently corrects "./path/etc" and "//www.domain.com/etc"
		$path = preg_replace($regexp, '', $path);

		if (substr($path, 0, 1) == '#') {
			return;
		}
		// mPDF 5.7.4
		if (substr($path, 0, 7) == "mailto:") {
			return;
		}
		if (substr($path, 0, 3) == "../") { //It is a Relative Link
			$backtrackamount = substr_count($path, "../");
			$maxbacktrack = substr_count($basepath, "/") - 3;
			$filepath = str_replace("../", '', $path);
			$path = $basepath;
			//If it is an invalid relative link, then make it go to directory root
			if ($backtrackamount > $maxbacktrack)
				$backtrackamount = $maxbacktrack;
			//Backtrack some directories
			for ($i = 0; $i < $backtrackamount + 1; $i++)
				$path = substr($path, 0, strrpos($path, "/"));
			$path = $path . "/" . $filepath; //Make it an absolute path
		}
		else if (strpos($path, ":/") === false || strpos($path, ":/") > 10) { //It is a Local Link
			if (substr($path, 0, 1) == "/") {
				$tr = parse_url($basepath);
				// mPDF 5.7.2
				$root = '';
				if (!empty($tr['scheme'])) {
					$root .= $tr['scheme'] . '://';
				}
				$root .= $tr['host'];
				$root .= ((isset($tr['port']) && $tr['port']) ? (':' . $tr['port']) : ''); // mPDF 5.7.3
				$path = $root . $path;
			} else {
				$path = $basepath . $path;
			}
		}
		//Do nothing if it is an Absolute Link
	}

// Used for external CSS files
	function _get_file($path)
	{
		// If local file try using local path (? quicker, but also allowed even if allow_url_fopen false)
		$contents = '';
		// mPDF 5.7.3
		if (strpos($path, "//") === false) {
			$path = preg_replace('/\.css\?.*$/', '.css', $path);
		}
		$contents = @file_get_contents($path);
		if ($contents) {
			return $contents;
		}
		if ($this->basepathIsLocal) {
			$tr = parse_url($path);
			$lp = getenv("SCRIPT_NAME");
			$ap = realpath($lp);
			$ap = str_replace("\\", "/", $ap);
			$docroot = substr($ap, 0, strpos($ap, $lp));
			// WriteHTML parses all paths to full URLs; may be local file name
			if ($tr['scheme'] && $tr['host'] && $_SERVER["DOCUMENT_ROOT"]) {
				$localpath = $_SERVER["DOCUMENT_ROOT"] . $tr['path'];
			}
			// DOCUMENT_ROOT is not returned on IIS
			else if ($docroot) {
				$localpath = $docroot . $tr['path'];
			} else {
				$localpath = $path;
			}
			$contents = @file_get_contents($localpath);
		}
		// if not use full URL
		else if (!$contents && !ini_get('allow_url_fopen') && function_exists("curl_init")) {
			$ch = curl_init($path);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$contents = curl_exec($ch);
			curl_close($ch);
		}
		return $contents;
	}

	function docPageNum($num = 0, $extras = false)
	{
		if ($num < 1) {
			$num = $this->page;
		}
		$type = $this->defaultPageNumStyle; // set default Page Number Style
		$ppgno = $num;
		$suppress = 0;
		$offset = 0;
		$lastreset = 0;
		foreach ($this->PageNumSubstitutions AS $psarr) {
			if ($num >= $psarr['from']) {
				if ($psarr['reset']) {
					if ($psarr['reset'] > 1) {
						$offset = $psarr['reset'] - 1;
					}
					$ppgno = $num - $psarr['from'] + 1 + $offset;
					$lastreset = $psarr['from'];
				}
				if ($psarr['type']) {
					$type = $psarr['type'];
				}
				if (strtoupper($psarr['suppress']) == 'ON' || $psarr['suppress'] == 1) {
					$suppress = 1;
				} else if (strtoupper($psarr['suppress']) == 'OFF') {
					$suppress = 0;
				}
			}
		}
		if ($suppress) {
			return '';
		}

		$ppgno = $this->_getStyledNumber($ppgno, $type);
		if ($extras) {
			$ppgno = $this->pagenumPrefix . $ppgno . $this->pagenumSuffix;
		}
		return $ppgno;
	}

	function docPageNumTotal($num = 0, $extras = false)
	{
		if ($num < 1) {
			$num = $this->page;
		}
		$type = $this->defaultPageNumStyle; // set default Page Number Style
		$ppgstart = 1;
		$ppgend = count($this->pages) + 1;
		$suppress = 0;
		$offset = 0;
		foreach ($this->PageNumSubstitutions AS $psarr) {
			if ($num >= $psarr['from']) {
				if ($psarr['reset']) {
					if ($psarr['reset'] > 1) {
						$offset = $psarr['reset'] - 1;
					}
					$ppgstart = $psarr['from'] + $offset;
					$ppgend = count($this->pages) + 1 + $offset;
				}
				if ($psarr['type']) {
					$type = $psarr['type'];
				}
				if (strtoupper($psarr['suppress']) == 'ON' || $psarr['suppress'] == 1) {
					$suppress = 1;
				} else if (strtoupper($psarr['suppress']) == 'OFF') {
					$suppress = 0;
				}
			}
			if ($num < $psarr['from']) {
				if ($psarr['reset']) {
					$ppgend = $psarr['from'] + $offset;
					break;
				}
			}
		}
		if ($suppress) {
			return '';
		}
		$ppgno = $ppgend - $ppgstart + $offset;

		$ppgno = $this->_getStyledNumber($ppgno, $type);
		if ($extras) {
			$ppgno = $this->pagenumPrefix . $ppgno . $this->pagenumSuffix;
		}
		return $ppgno;
	}

// mPDF 6
	function _getStyledNumber($ppgno, $type, $listmarker = false)
	{
		if ($listmarker) {
			$reverse = true;   // Reverse RTL numerals (Hebrew) when using for list
			$checkfont = true; // Using list - font is set, so check if character is available
		} else {
			$reverse = false;  // For pagenumbers, RTL numerals (Hebrew) will get reversed later by bidi
			$checkfont = false; // For pagenumbers - font is not set, so no check
		}
		$lowertype = strtolower($type);
		if ($lowertype == 'upper-latin' || $lowertype == 'upper-alpha' || $type == 'A') {
			$ppgno = $this->dec2alpha($ppgno, true);
		} else if ($lowertype == 'lower-latin' || $lowertype == 'lower-alpha' || $type == 'a') {
			$ppgno = $this->dec2alpha($ppgno, false);
		} else if ($lowertype == 'upper-roman' || $type == 'I') {
			$ppgno = $this->dec2roman($ppgno, true);
		} else if ($lowertype == 'lower-roman' || $type == 'i') {
			$ppgno = $this->dec2roman($ppgno, false);
		} else if ($lowertype == 'hebrew') {
			$ppgno = $this->dec2hebrew($ppgno, $reverse);
		} else if (preg_match('/(arabic-indic|bengali|devanagari|gujarati|gurmukhi|kannada|malayalam|oriya|persian|tamil|telugu|thai|urdu|cambodian|khmer|lao)/i', $lowertype, $m)) {
			switch ($m[1]) { //Format type
				case 'arabic-indic': $cp = 0x0660;
					break;
				case 'persian':
				case 'urdu': $cp = 0x06F0;
					break;
				case 'bengali': $cp = 0x09E6;
					break;
				case 'devanagari': $cp = 0x0966;
					break;
				case 'gujarati': $cp = 0x0AE6;
					break;
				case 'gurmukhi': $cp = 0x0A66;
					break;
				case 'kannada': $cp = 0x0CE6;
					break;
				case 'malayalam': $cp = 0x0D66;
					break;
				case 'oriya': $cp = 0x0B66;
					break;
				case 'telugu': $cp = 0x0C66;
					break;
				case 'tamil': $cp = 0x0BE6;
					break;
				case 'thai': $cp = 0x0E50;
					break;
				case 'khmer':
				case 'cambodian': $cp = 0x17E0;
					break;
				case 'lao': $cp = 0x0ED0;
					break;
			}
			$ppgno = $this->dec2other($ppgno, $cp, $checkfont);
		} else if ($lowertype == 'cjk-decimal') {
			$ppgno = $this->dec2cjk($ppgno);
		}
		return $ppgno;
	}

	function docPageSettings($num = 0)
	{
		// Returns current type (numberstyle), suppression state for this page number;
		// reset is only returned if set for this page number
		if ($num < 1) {
			$num = $this->page;
		}
		$type = $this->defaultPageNumStyle; // set default Page Number Style
		$ppgno = $num;
		$suppress = 0;
		$offset = 0;
		$reset = '';
		foreach ($this->PageNumSubstitutions AS $psarr) {
			if ($num >= $psarr['from']) {
				if ($psarr['reset']) {
					if ($psarr['reset'] > 1) {
						$offset = $psarr['reset'] - 1;
					}
					$ppgno = $num - $psarr['from'] + 1 + $offset;
				}
				if ($psarr['type']) {
					$type = $psarr['type'];
				}
				if (strtoupper($psarr['suppress']) == 'ON' || $psarr['suppress'] == 1) {
					$suppress = 1;
				} else if (strtoupper($psarr['suppress']) == 'OFF') {
					$suppress = 0;
				}
			}
			if ($num == $psarr['from']) {
				$reset = $psarr['reset'];
			}
		}
		if ($suppress) {
			$suppress = 'on';
		} else {
			$suppress = 'off';
		}
		return array($type, $suppress, $reset);
	}

	function RestartDocTemplate()
	{
		$this->docTemplateStart = $this->page;
	}

//Page header
	function Header($content = '')
	{

		$this->cMarginL = 0;
		$this->cMarginR = 0;


		if (($this->mirrorMargins && ($this->page % 2 == 0) && $this->HTMLHeaderE) || ($this->mirrorMargins && ($this->page % 2 == 1) && $this->HTMLHeader) || (!$this->mirrorMargins && $this->HTMLHeader)) {
			$this->writeHTMLHeaders();
			return;
		}
	}

	/* -- TABLES -- */

	function TableHeaderFooter($content = '', $tablestartpage = '', $tablestartcolumn = '', $horf = 'H', $level, $firstSpread = true, $finalSpread = true)
	{
		if (($horf == 'H' || $horf == 'F') && !empty($content)) { // mPDF 5.7.2
			$table = &$this->table[1][1];

			// mPDF 5.7.2
			if ($horf == 'F') { // Table Footer
				$firstrow = count($table['cells']) - $table['footernrows'];
				$lastrow = count($table['cells']) - 1;
			} else {  // Table Header
				$firstrow = 0;
				$lastrow = $table['headernrows'] - 1;
			}
			if (empty($content[$firstrow])) {
				if ($this->debug) {
					$this->Error("&lt;tfoot&gt; must precede &lt;tbody&gt; in a table");
				} else {
					return;
				}
			}


			// Advance down page by half width of top border
			if ($horf == 'H') { // Only if header
				if ($table['borders_separate']) {
					$adv = $table['border_spacing_V'] / 2 + $table['border_details']['T']['w'] + $table['padding']['T'];
				} else {
					$adv = $table['max_cell_border_width']['T'] / 2;
				}
				if ($adv) {
					if ($this->table_rotate) {
						$this->y += ($adv);
					} else {
						$this->DivLn($adv, $this->blklvl, true);
					}
				}
			}

			$topy = $content[$firstrow][0]['y'] - $this->y;

			for ($i = $firstrow; $i <= $lastrow; $i++) {

				$y = $this->y;

				/* -- COLUMNS -- */
				// If outside columns, this is done in PaintDivBB
				if ($this->ColActive) {
					//OUTER FILL BGCOLOR of DIVS
					if ($this->blklvl > 0) {
						$firstblockfill = $this->GetFirstBlockFill();
						if ($firstblockfill && $this->blklvl >= $firstblockfill) {
							$divh = $content[$i][0]['h'];
							$bak_x = $this->x;
							$this->DivLn($divh, -3, false);
							// Reset current block fill
							$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
							$this->SetFColor($bcor);
							$this->x = $bak_x;
						}
					}
				}
				/* -- END COLUMNS -- */

				$colctr = 0;
				foreach ($content[$i] as $tablehf) {
					$colctr++;
					$y = $tablehf['y'] - $topy;
					$this->y = $y;
					//Set some cell values
					$x = $tablehf['x'];
					if (($this->mirrorMargins) && ($tablestartpage == 'ODD') && (($this->page) % 2 == 0)) { // EVEN
						$x = $x + $this->MarginCorrection;
					} else if (($this->mirrorMargins) && ($tablestartpage == 'EVEN') && (($this->page) % 2 == 1)) { // ODD
						$x = $x + $this->MarginCorrection;
					}
					/* -- COLUMNS -- */
					// Added to correct for Columns
					if ($this->ColActive) {
						if ($this->directionality == 'rtl') { // *OTL*
							$x -= ($this->CurrCol - $tablestartcolumn) * ($this->ColWidth + $this->ColGap); // *OTL*
						} // *OTL*
						else { // *OTL*
							$x += ($this->CurrCol - $tablestartcolumn) * ($this->ColWidth + $this->ColGap);
						} // *OTL*
					}
					/* -- END COLUMNS -- */

					if ($colctr == 1) {
						$x0 = $x;
					}

					// mPDF ITERATION
					if ($this->iterationCounter) {
						foreach ($tablehf['textbuffer'] AS $k => $t) {
							if (!is_array($t[0]) && preg_match('/{iteration ([a-zA-Z0-9_]+)}/', $t[0], $m)) {
								$vname = '__' . $m[1] . '_';
								if (!isset($this->$vname)) {
									$this->$vname = 1;
								} else {
									$this->$vname++;
								}
								$tablehf['textbuffer'][$k][0] = preg_replace('/{iteration ' . $m[1] . '}/', $this->$vname, $tablehf['textbuffer'][$k][0]);
							}
						}
					}

					$w = $tablehf['w'];
					$h = $tablehf['h'];
					$va = $tablehf['va'];
					$R = $tablehf['R'];
					$direction = $tablehf['direction'];
					$mih = $tablehf['mih'];
					$border = $tablehf['border'];
					$border_details = $tablehf['border_details'];
					$padding = $tablehf['padding'];
					$this->tabletheadjustfinished = true;

					$textbuffer = $tablehf['textbuffer'];

					//Align
					$align = $tablehf['a'];
					$this->cellTextAlign = $align;

					$this->cellLineHeight = $tablehf['cellLineHeight'];
					$this->cellLineStackingStrategy = $tablehf['cellLineStackingStrategy'];
					$this->cellLineStackingShift = $tablehf['cellLineStackingShift'];

					$this->x = $x;

					if ($this->ColActive) {
						if ($table['borders_separate']) {
							$tablefill = isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0;
							if ($tablefill) {
								$color = $this->ConvertColor($tablefill);
								if ($color) {
									$xadj = ($table['border_spacing_H'] / 2);
									$yadj = ($table['border_spacing_V'] / 2);
									$wadj = $table['border_spacing_H'];
									$hadj = $table['border_spacing_V'];
									if ($i == $firstrow && $horf == 'H') {  // Top
										$yadj += $table['padding']['T'] + $table['border_details']['T']['w'];
										$hadj += $table['padding']['T'] + $table['border_details']['T']['w'];
									}
									if (($i == ($lastrow) || (isset($tablehf['rowspan']) && ($i + $tablehf['rowspan']) == ($lastrow + 1)) || (!isset($tablehf['rowspan']) && ($i + 1) == ($lastrow + 1))) && $horf == 'F') { // Bottom
										$hadj += $table['padding']['B'] + $table['border_details']['B']['w'];
									}
									if ($colctr == 1) {  // Left
										$xadj += $table['padding']['L'] + $table['border_details']['L']['w'];
										$wadj += $table['padding']['L'] + $table['border_details']['L']['w'];
									}
									if ($colctr == count($content[$i])) { // Right
										$wadj += $table['padding']['R'] + $table['border_details']['R']['w'];
									}
									$this->SetFColor($color);
									$this->Rect($x - $xadj, $y - $yadj, $w + $wadj, $h + $hadj, 'F');
								}
							}
						}
					}

					if ($table['empty_cells'] != 'hide' || !empty($textbuffer) || !$table['borders_separate']) {
						$paintcell = true;
					} else {
						$paintcell = false;
					}

					//Vertical align
					if ($R && INTVAL($R) > 0 && isset($va) && $va != 'B') {
						$va = 'B';
					}

					if (!isset($va) || empty($va) || $va == 'M')
						$this->y += ($h - $mih) / 2;
					elseif (isset($va) && $va == 'B')
						$this->y += $h - $mih;


					//TABLE ROW OR CELL FILL BGCOLOR
					$fill = 0;
					if (isset($tablehf['bgcolor']) && $tablehf['bgcolor'] && $tablehf['bgcolor'] != 'transparent') {
						$fill = $tablehf['bgcolor'];
						$leveladj = 6;
					} else if (isset($content[$i][0]['trbgcolor']) && $content[$i][0]['trbgcolor'] && $content[$i][0]['trbgcolor'] != 'transparent') { // Row color
						$fill = $content[$i][0]['trbgcolor'];
						$leveladj = 3;
					}
					if ($fill && $paintcell) {
						$color = $this->ConvertColor($fill);
						if ($color) {
							if ($table['borders_separate']) {
								if ($this->ColActive) {
									$this->SetFColor($color);
									$this->Rect($x + ($table['border_spacing_H'] / 2), $y + ($table['border_spacing_V'] / 2), $w - $table['border_spacing_H'], $h - $table['border_spacing_V'], 'F');
								} else {
									$this->tableBackgrounds[$level * 9 + $leveladj][] = array('gradient' => false, 'x' => ($x + ($table['border_spacing_H'] / 2)), 'y' => ($y + ($table['border_spacing_V'] / 2)), 'w' => ($w - $table['border_spacing_H']), 'h' => ($h - $table['border_spacing_V']), 'col' => $color);
								}
							} else {
								if ($this->ColActive) {
									$this->SetFColor($color);
									$this->Rect($x, $y, $w, $h, 'F');
								} else {
									$this->tableBackgrounds[$level * 9 + $leveladj][] = array('gradient' => false, 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'col' => $color);
								}
							}
						}
					}


					/* -- BACKGROUNDS -- */
					if (isset($tablehf['gradient']) && $tablehf['gradient'] && $paintcell) {
						$g = $this->grad->parseBackgroundGradient($tablehf['gradient']);
						if ($g) {
							if ($table['borders_separate']) {
								$px = $x + ($table['border_spacing_H'] / 2);
								$py = $y + ($table['border_spacing_V'] / 2);
								$pw = $w - $table['border_spacing_H'];
								$ph = $h - $table['border_spacing_V'];
							} else {
								$px = $x;
								$py = $y;
								$pw = $w;
								$ph = $h;
							}
							if ($this->ColActive) {
								$this->grad->Gradient($px, $py, $pw, $ph, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend']);
							} else {
								$this->tableBackgrounds[$level * 9 + 7][] = array('gradient' => true, 'x' => $px, 'y' => $py, 'w' => $pw, 'h' => $ph, 'gradtype' => $g['type'], 'stops' => $g['stops'], 'colorspace' => $g['colorspace'], 'coords' => $g['coords'], 'extend' => $g['extend'], 'clippath' => '');
							}
						}
					}

					if (isset($tablehf['background-image']) && $paintcell) {
						if ($tablehf['background-image']['gradient'] && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $tablehf['background-image']['gradient'])) {
							$g = $this->grad->parseMozGradient($tablehf['background-image']['gradient']);
							if ($g) {
								if ($table['borders_separate']) {
									$px = $x + ($table['border_spacing_H'] / 2);
									$py = $y + ($table['border_spacing_V'] / 2);
									$pw = $w - $table['border_spacing_H'];
									$ph = $h - $table['border_spacing_V'];
								} else {
									$px = $x;
									$py = $y;
									$pw = $w;
									$ph = $h;
								}
								if ($this->ColActive) {
									$this->grad->Gradient($px, $py, $pw, $ph, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend']);
								} else {
									$this->tableBackgrounds[$level * 9 + 7][] = array('gradient' => true, 'x' => $px, 'y' => $py, 'w' => $pw, 'h' => $ph, 'gradtype' => $g['type'], 'stops' => $g['stops'], 'colorspace' => $g['colorspace'], 'coords' => $g['coords'], 'extend' => $g['extend'], 'clippath' => '');
								}
							}
						} else if ($tablehf['background-image']['image_id']) { // Background pattern
							$n = count($this->patterns) + 1;
							if ($table['borders_separate']) {
								$px = $x + ($table['border_spacing_H'] / 2);
								$py = $y + ($table['border_spacing_V'] / 2);
								$pw = $w - $table['border_spacing_H'];
								$ph = $h - $table['border_spacing_V'];
							} else {
								$px = $x;
								$py = $y;
								$pw = $w;
								$ph = $h;
							}
							if ($this->ColActive) {
								list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($tablehf['background-image']['orig_w'], $tablehf['background-image']['orig_h'], $pw, $ph, $tablehf['background-image']['resize'], $tablehf['background-image']['x_repeat'], $tablehf['background-image']['y_repeat']);
								$this->patterns[$n] = array('x' => $px, 'y' => $py, 'w' => $pw, 'h' => $ph, 'pgh' => $this->h, 'image_id' => $tablehf['background-image']['image_id'], 'orig_w' => $orig_w, 'orig_h' => $orig_h, 'x_pos' => $tablehf['background-image']['x_pos'], 'y_pos' => $tablehf['background-image']['y_pos'], 'x_repeat' => $x_repeat, 'y_repeat' => $y_repeat, 'itype' => $tablehf['background-image']['itype']);
								if ($tablehf['background-image']['opacity'] > 0 && $tablehf['background-image']['opacity'] < 1) {
									$opac = $this->SetAlpha($tablehf['background-image']['opacity'], 'Normal', true);
								} else {
									$opac = '';
								}
								$this->_out(sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, $px * _MPDFK, ($this->h - $py) * _MPDFK, $pw * _MPDFK, -$ph * _MPDFK));
							} else {
								$this->tableBackgrounds[$level * 9 + 8][] = array('x' => $px, 'y' => $py, 'w' => $pw, 'h' => $ph, 'image_id' => $tablehf['background-image']['image_id'], 'orig_w' => $tablehf['background-image']['orig_w'], 'orig_h' => $tablehf['background-image']['orig_h'], 'x_pos' => $tablehf['background-image']['x_pos'], 'y_pos' => $tablehf['background-image']['y_pos'], 'x_repeat' => $tablehf['background-image']['x_repeat'], 'y_repeat' => $tablehf['background-image']['y_repeat'], 'clippath' => '', 'resize' => $tablehf['background-image']['resize'], 'opacity' => $tablehf['background-image']['opacity'], 'itype' => $tablehf['background-image']['itype']);
							}
						}
					}
					/* -- END BACKGROUNDS -- */

					//Cell Border
					if ($table['borders_separate'] && $paintcell && $border) {
						$this->_tableRect($x + ($table['border_spacing_H'] / 2) + ($border_details['L']['w'] / 2), $y + ($table['border_spacing_V'] / 2) + ($border_details['T']['w'] / 2), $w - $table['border_spacing_H'] - ($border_details['L']['w'] / 2) - ($border_details['R']['w'] / 2), $h - $table['border_spacing_V'] - ($border_details['T']['w'] / 2) - ($border_details['B']['w'] / 2), $border, $border_details, false, $table['borders_separate']);
					} else if ($paintcell && $border) {
						$this->_tableRect($x, $y, $w, $h, $border, $border_details, true, $table['borders_separate']);   // true causes buffer
					}

					//Print cell content
					if (!empty($textbuffer)) {
						if ($horf == 'F' && preg_match('/{colsum([0-9]*)[_]*}/', $textbuffer[0][0], $m)) {
							$rep = sprintf("%01." . intval($m[1]) . "f", $this->colsums[$colctr - 1]);
							$textbuffer[0][0] = preg_replace('/{colsum[0-9_]*}/', $rep, $textbuffer[0][0]);
						}

						if ($R) {
							$cellPtSize = $textbuffer[0][11] / $this->shrin_k;
							if (!$cellPtSize) {
								$cellPtSize = $this->default_font_size;
							}
							$cellFontHeight = ($cellPtSize / _MPDFK);
							$opx = $this->x;
							$opy = $this->y;
							$angle = INTVAL($R);
							// Only allow 45 - 90 degrees (when bottom-aligned) or -90
							if ($angle > 90) {
								$angle = 90;
							} else if ($angle > 0 && (isset($va) && $va != 'B')) {
								$angle = 90;
							} else if ($angle > 0 && $angle < 45) {
								$angle = 45;
							} else if ($angle < 0) {
								$angle = -90;
							}
							$offset = ((sin(deg2rad($angle))) * 0.37 * $cellFontHeight);
							if (isset($align) && $align == 'R') {
								$this->x += ($w) + ($offset) - ($cellFontHeight / 3) - ($padding['R'] + $border_details['R']['w']);
							} else if (!isset($align) || $align == 'C') {
								$this->x += ($w / 2) + ($offset);
							} else {
								$this->x += ($offset) + ($cellFontHeight / 3) + ($padding['L'] + $border_details['L']['w']);
							}
							$str = '';
							foreach ($tablehf['textbuffer'] AS $t) {
								$str .= $t[0] . ' ';
							}
							$str = rtrim($str);

							if (!isset($va) || $va == 'M') {
								$this->y -= ($h - $mih) / 2; //Undo what was added earlier VERTICAL ALIGN
								if ($angle > 0) {
									$this->y += (($h - $mih) / 2) + ($padding['T'] + $border_details['T']['w']) + ($mih - ($padding['T'] + $border_details['T']['w'] + $border_details['B']['w'] + $padding['B']));
								} else if ($angle < 0) {
									$this->y += (($h - $mih) / 2) + ($padding['T'] + $border_details['T']['w']);
								}
							} else if (isset($va) && $va == 'B') {
								$this->y -= $h - $mih; //Undo what was added earlier VERTICAL ALIGN
								if ($angle > 0) {
									$this->y += $h - ($border_details['B']['w'] + $padding['B']);
								} else if ($angle < 0) {
									$this->y += $h - $mih + ($padding['T'] + $border_details['T']['w']);
								}
							} else if (isset($va) && $va == 'T') {
								if ($angle > 0) {
									$this->y += $mih - ($border_details['B']['w'] + $padding['B']);
								} else if ($angle < 0) {
									$this->y += ($padding['T'] + $border_details['T']['w']);
								}
							}

							$this->Rotate($angle, $this->x, $this->y);
							$s_fs = $this->FontSizePt;
							$s_f = $this->FontFamily;
							$s_st = $this->FontStyle;
							if (!empty($textbuffer[0][3])) { //Font Color
								$cor = $textbuffer[0][3];
								$this->SetTColor($cor);
							}
							$this->SetFont($textbuffer[0][4], $textbuffer[0][2], $cellPtSize, true, true);

							$this->magic_reverse_dir($str, $this->directionality, $textbuffer[0][18]);
							$this->Text($this->x, $this->y, $str, $textbuffer[0][18], $textbuffer[0][8]); // textvar
							$this->Rotate(0);
							$this->SetFont($s_f, $s_st, $s_fs, true, true);
							$this->SetTColor(0);
							$this->x = $opx;
							$this->y = $opy;
						} else {
							if ($table['borders_separate']) { // NB twice border width
								$xadj = $border_details['L']['w'] + $padding['L'] + ($table['border_spacing_H'] / 2);
								$wadj = $border_details['L']['w'] + $border_details['R']['w'] + $padding['L'] + $padding['R'] + $table['border_spacing_H'];
								$yadj = $border_details['T']['w'] + $padding['T'] + ($table['border_spacing_H'] / 2);
							} else {
								$xadj = $border_details['L']['w'] / 2 + $padding['L'];
								$wadj = ($border_details['L']['w'] + $border_details['R']['w']) / 2 + $padding['L'] + $padding['R'];
								$yadj = $border_details['T']['w'] / 2 + $padding['T'];
							}

							$this->divwidth = $w - ($wadj);
							$this->x += $xadj;
							$this->y += $yadj;
							$this->printbuffer($textbuffer, '', true, false, $direction);
						}
					}
					$textbuffer = array();

					/* -- BACKGROUNDS -- */
					if (!$this->ColActive) {
						if (isset($content[$i][0]['trgradients']) && ($colctr == 1 || $table['borders_separate'])) {
							$g = $this->grad->parseBackgroundGradient($content[$i][0]['trgradients']);
							if ($g) {
								$gx = $x0;
								$gy = $y;
								$gh = $h;
								$gw = $table['w'] - ($table['max_cell_border_width']['L'] / 2) - ($table['max_cell_border_width']['R'] / 2) - $table['margin']['L'] - $table['margin']['R'];
								if ($table['borders_separate']) {
									$gw -= ($table['padding']['L'] + $table['border_details']['L']['w'] + $table['padding']['R'] + $table['border_details']['R']['w'] + $table['border_spacing_H']);
									$clx = $x + ($table['border_spacing_H'] / 2);
									$cly = $y + ($table['border_spacing_V'] / 2);
									$clw = $w - $table['border_spacing_H'];
									$clh = $h - $table['border_spacing_V'];
									// Set clipping path
									$s = $this->_setClippingPath($clx, $cly, $clw, $clh); // mPDF 6
									$this->tableBackgrounds[$level * 9 + 4][] = array('gradient' => true, 'x' => $gx + ($table['border_spacing_H'] / 2), 'y' => $gy + ($table['border_spacing_V'] / 2), 'w' => $gw - $table['border_spacing_V'], 'h' => $gh - $table['border_spacing_H'], 'gradtype' => $g['type'], 'stops' => $g['stops'], 'colorspace' => $g['colorspace'], 'coords' => $g['coords'], 'extend' => $g['extend'], 'clippath' => $s);
								} else {
									$this->tableBackgrounds[$level * 9 + 4][] = array('gradient' => true, 'x' => $gx, 'y' => $gy, 'w' => $gw, 'h' => $gh, 'gradtype' => $g['type'], 'stops' => $g['stops'], 'colorspace' => $g['colorspace'], 'coords' => $g['coords'], 'extend' => $g['extend'], 'clippath' => '');
								}
							}
						}

						if (isset($content[$i][0]['trbackground-images']) && ($colctr == 1 || $table['borders_separate'])) {
							if ($content[$i][0]['trbackground-images']['gradient'] && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $content[$i][0]['trbackground-images']['gradient'])) {
								$g = $this->grad->parseMozGradient($content[$i][0]['trbackground-images']['gradient']);
								if ($g) {
									$gx = $x0;
									$gy = $y;
									$gh = $h;
									$gw = $table['w'] - ($table['max_cell_border_width']['L'] / 2) - ($table['max_cell_border_width']['R'] / 2) - $table['margin']['L'] - $table['margin']['R'];
									if ($table['borders_separate']) {
										$gw -= ($table['padding']['L'] + $table['border_details']['L']['w'] + $table['padding']['R'] + $table['border_details']['R']['w'] + $table['border_spacing_H']);
										$clx = $x + ($table['border_spacing_H'] / 2);
										$cly = $y + ($table['border_spacing_V'] / 2);
										$clw = $w - $table['border_spacing_H'];
										$clh = $h - $table['border_spacing_V'];
										// Set clipping path
										$s = $this->_setClippingPath($clx, $cly, $clw, $clh); // mPDF 6
										$this->tableBackgrounds[$level * 9 + 4][] = array('gradient' => true, 'x' => $gx + ($table['border_spacing_H'] / 2), 'y' => $gy + ($table['border_spacing_V'] / 2), 'w' => $gw - $table['border_spacing_V'], 'h' => $gh - $table['border_spacing_H'], 'gradtype' => $g['type'], 'stops' => $g['stops'], 'colorspace' => $g['colorspace'], 'coords' => $g['coords'], 'extend' => $g['extend'], 'clippath' => $s);
									} else {
										$this->tableBackgrounds[$level * 9 + 4][] = array('gradient' => true, 'x' => $gx, 'y' => $gy, 'w' => $gw, 'h' => $gh, 'gradtype' => $g['type'], 'stops' => $g['stops'], 'colorspace' => $g['colorspace'], 'coords' => $g['coords'], 'extend' => $g['extend'], 'clippath' => '');
									}
								}
							} else {
								$image_id = $content[$i][0]['trbackground-images']['image_id'];
								$orig_w = $content[$i][0]['trbackground-images']['orig_w'];
								$orig_h = $content[$i][0]['trbackground-images']['orig_h'];
								$x_pos = $content[$i][0]['trbackground-images']['x_pos'];
								$y_pos = $content[$i][0]['trbackground-images']['y_pos'];
								$x_repeat = $content[$i][0]['trbackground-images']['x_repeat'];
								$y_repeat = $content[$i][0]['trbackground-images']['y_repeat'];
								$resize = $content[$i][0]['trbackground-images']['resize'];
								$opacity = $content[$i][0]['trbackground-images']['opacity'];
								$itype = $content[$i][0]['trbackground-images']['itype'];

								$clippath = '';
								$gx = $x0;
								$gy = $y;
								$gh = $h;
								$gw = $table['w'] - ($table['max_cell_border_width']['L'] / 2) - ($table['max_cell_border_width']['R'] / 2) - $table['margin']['L'] - $table['margin']['R'];
								if ($table['borders_separate']) {
									$gw -= ($table['padding']['L'] + $table['border_details']['L']['w'] + $table['padding']['R'] + $table['border_details']['R']['w'] + $table['border_spacing_H']);
									$clx = $x + ($table['border_spacing_H'] / 2);
									$cly = $y + ($table['border_spacing_V'] / 2);
									$clw = $w - $table['border_spacing_H'];
									$clh = $h - $table['border_spacing_V'];
									// Set clipping path
									$s = $this->_setClippingPath($clx, $cly, $clw, $clh); // mPDF 6
									$this->tableBackgrounds[$level * 9 + 5][] = array('x' => $gx + ($table['border_spacing_H'] / 2), 'y' => $gy + ($table['border_spacing_V'] / 2), 'w' => $gw - $table['border_spacing_V'], 'h' => $gh - $table['border_spacing_H'], 'image_id' => $image_id, 'orig_w' => $orig_w, 'orig_h' => $orig_h, 'x_pos' => $x_pos, 'y_pos' => $y_pos, 'x_repeat' => $x_repeat, 'y_repeat' => $y_repeat, 'clippath' => $s, 'resize' => $resize, 'opacity' => $opacity, 'itype' => $itype);
								} else {
									$this->tableBackgrounds[$level * 9 + 5][] = array('x' => $gx, 'y' => $gy, 'w' => $gw, 'h' => $gh, 'image_id' => $image_id, 'orig_w' => $orig_w, 'orig_h' => $orig_h, 'x_pos' => $x_pos, 'y_pos' => $y_pos, 'x_repeat' => $x_repeat, 'y_repeat' => $y_repeat, 'clippath' => '', 'resize' => $resize, 'opacity' => $opacity, 'itype' => $itype);
								}
							}
						}
					}
					/* -- END BACKGROUNDS -- */

					// TABLE BORDER - if separate OR collapsed and only table border
					if (($table['borders_separate'] || ($this->simpleTables && !$table['simple']['border'])) && $table['border']) {
						$halfspaceL = $table['padding']['L'] + ($table['border_spacing_H'] / 2);
						$halfspaceR = $table['padding']['R'] + ($table['border_spacing_H'] / 2);
						$halfspaceT = $table['padding']['T'] + ($table['border_spacing_V'] / 2);
						$halfspaceB = $table['padding']['B'] + ($table['border_spacing_V'] / 2);
						$tbx = $x;
						$tby = $y;
						$tbw = $w;
						$tbh = $h;
						$tab_bord = 0;
						$corner = '';
						if ($i == $firstrow && $horf == 'H') {  // Top
							$tby -= $halfspaceT + ($table['border_details']['T']['w'] / 2);
							$tbh += $halfspaceT + ($table['border_details']['T']['w'] / 2);
							$this->setBorder($tab_bord, _BORDER_TOP);
							$corner .= 'T';
						}
						if (($i == ($lastrow) || (isset($tablehf['rowspan']) && ($i + $tablehf['rowspan']) == ($lastrow + 1))) && $horf == 'F') { // Bottom
							$tbh += $halfspaceB + ($table['border_details']['B']['w'] / 2);
							$this->setBorder($tab_bord, _BORDER_BOTTOM);
							$corner .= 'B';
						}
						if ($colctr == 1 && $firstSpread) { // Left
							$tbx -= $halfspaceL + ($table['border_details']['L']['w'] / 2);
							$tbw += $halfspaceL + ($table['border_details']['L']['w'] / 2);
							$this->setBorder($tab_bord, _BORDER_LEFT);
							$corner .= 'L';
						}
						if ($colctr == count($content[$i]) && $finalSpread) { // Right
							$tbw += $halfspaceR + ($table['border_details']['R']['w'] / 2);
							$this->setBorder($tab_bord, _BORDER_RIGHT);
							$corner .= 'R';
						}
						$this->_tableRect($tbx, $tby, $tbw, $tbh, $tab_bord, $table['border_details'], false, $table['borders_separate'], 'table', $corner, $table['border_spacing_V'], $table['border_spacing_H']);
					}
				}// end column $content
				$this->y = $y + $h; //Update y coordinate
			}// end row $i
			unset($table);
			$this->colsums = array();
		}
	}

	/* -- END TABLES -- */

	function SetHTMLHeader($header = '', $OE = '', $write = false)
	{

		$height = 0;
		if (is_array($header) && isset($header['html']) && $header['html']) {
			$Hhtml = $header['html'];
			if ($this->setAutoTopMargin) {
				if (isset($header['h'])) {
					$height = $header['h'];
				} else {
					$height = $this->_gethtmlheight($Hhtml);
				}
			}
		} else if (!is_array($header) && $header) {
			$Hhtml = $header;
			if ($this->setAutoTopMargin) {
				$height = $this->_gethtmlheight($Hhtml);
			}
		} else {
			$Hhtml = '';
		}

		if ($OE != 'E') {
			$OE = 'O';
		}
		if ($OE == 'E') {

			if ($Hhtml) {
				$this->HTMLHeaderE['html'] = $Hhtml;
				$this->HTMLHeaderE['h'] = $height;
			} else {
				$this->HTMLHeaderE = '';
			}
		} else {

			if ($Hhtml) {
				$this->HTMLHeader['html'] = $Hhtml;
				$this->HTMLHeader['h'] = $height;
			} else {
				$this->HTMLHeader = '';
			}
		}
		if (!$this->mirrorMargins && $OE == 'E') {
			return;
		}
		if ($Hhtml == '') {
			return;
		}

		if ($this->setAutoTopMargin == 'pad') {
			$this->tMargin = $this->margin_header + $height + $this->orig_tMargin;
			if (isset($this->saveHTMLHeader[$this->page][$OE]['mt'])) {
				$this->saveHTMLHeader[$this->page][$OE]['mt'] = $this->tMargin;
			}
		} else if ($this->setAutoTopMargin == 'stretch') {
			$this->tMargin = max($this->orig_tMargin, $this->margin_header + $height + $this->autoMarginPadding);
			if (isset($this->saveHTMLHeader[$this->page][$OE]['mt'])) {
				$this->saveHTMLHeader[$this->page][$OE]['mt'] = $this->tMargin;
			}
		}
		if ($write && $this->state != 0 && (($this->mirrorMargins && $OE == 'E' && ($this->page) % 2 == 0) || ($this->mirrorMargins && $OE != 'E' && ($this->page) % 2 == 1) || !$this->mirrorMargins)) {
			$this->writeHTMLHeaders();
		}
	}

	function SetHTMLFooter($footer = '', $OE = '')
	{
		$height = 0;
		if (is_array($footer) && isset($footer['html']) && $footer['html']) {
			$Fhtml = $footer['html'];
			if ($this->setAutoBottomMargin) {
				if (isset($footer['h'])) {
					$height = $footer['h'];
				} else {
					$height = $this->_gethtmlheight($Fhtml);
				}
			}
		} else if (!is_array($footer) && $footer) {
			$Fhtml = $footer;
			if ($this->setAutoBottomMargin) {
				$height = $this->_gethtmlheight($Fhtml);
			}
		} else {
			$Fhtml = '';
		}

		if ($OE != 'E') {
			$OE = 'O';
		}
		if ($OE == 'E') {

			if ($Fhtml) {
				$this->HTMLFooterE['html'] = $Fhtml;
				$this->HTMLFooterE['h'] = $height;
			} else {
				$this->HTMLFooterE = '';
			}
		} else {

			if ($Fhtml) {
				$this->HTMLFooter['html'] = $Fhtml;
				$this->HTMLFooter['h'] = $height;
			} else {
				$this->HTMLFooter = '';
			}
		}
		if (!$this->mirrorMargins && $OE == 'E') {
			return;
		}
		if ($Fhtml == '') {
			return false;
		}

		if ($this->setAutoBottomMargin == 'pad') {
			$this->bMargin = $this->margin_footer + $height + $this->orig_bMargin;
			$this->PageBreakTrigger = $this->h - $this->bMargin;
			if (isset($this->saveHTMLHeader[$this->page][$OE]['mb'])) {
				$this->saveHTMLHeader[$this->page][$OE]['mb'] = $this->bMargin;
			}
		} else if ($this->setAutoBottomMargin == 'stretch') {
			$this->bMargin = max($this->orig_bMargin, $this->margin_footer + $height + $this->autoMarginPadding);
			$this->PageBreakTrigger = $this->h - $this->bMargin;
			if (isset($this->saveHTMLHeader[$this->page][$OE]['mb'])) {
				$this->saveHTMLHeader[$this->page][$OE]['mb'] = $this->bMargin;
			}
		}
	}

	function _getHtmlHeight($html)
	{
		$save_state = $this->state;
		if ($this->state == 0) {
			$this->AddPage($this->CurOrientation);
		}
		$this->state = 2;
		$this->Reset();
		$this->pageoutput[$this->page] = array();
		$save_x = $this->x;
		$save_y = $this->y;
		$this->x = $this->lMargin;
		$this->y = $this->margin_header;
		$html = str_replace('{PAGENO}', $this->pagenumPrefix . $this->docPageNum($this->page) . $this->pagenumSuffix, $html);
		$html = str_replace($this->aliasNbPgGp, $this->nbpgPrefix . $this->docPageNumTotal($this->page) . $this->nbpgSuffix, $html);
		$html = str_replace($this->aliasNbPg, $this->page, $html);
		$html = preg_replace_callback('/\{DATE\s+(.*?)\}/', array($this, 'date_callback'), $html); // mPDF 5.7
		$this->HTMLheaderPageLinks = array();
		$this->HTMLheaderPageAnnots = array();
		$this->HTMLheaderPageForms = array();
		$savepb = $this->pageBackgrounds;
		$this->writingHTMLheader = true;
		$this->WriteHTML($html, 4); // parameter 4 saves output to $this->headerbuffer
		$this->writingHTMLheader = false;
		$h = ($this->y - $this->margin_header);
		$this->Reset();
		// mPDF 5.7.2 - Clear in case Float used in Header/Footer
		$this->blk[0]['blockContext'] = 0;
		$this->blk[0]['float_endpos'] = 0;

		$this->pageoutput[$this->page] = array();
		$this->headerbuffer = '';
		$this->pageBackgrounds = $savepb;
		$this->x = $save_x;
		$this->y = $save_y;
		$this->state = $save_state;
		if ($save_state == 0) {
			unset($this->pages[1]);
			$this->page = 0;
		}
		return $h;
	}

// Called internally from Header
	function writeHTMLHeaders()
	{

		if ($this->mirrorMargins && ($this->page) % 2 == 0) {
			$OE = 'E';
		} // EVEN
		else {
			$OE = 'O';
		}
		if ($OE == 'E') {
			$this->saveHTMLHeader[$this->page][$OE]['html'] = $this->HTMLHeaderE['html'];
		} else {
			$this->saveHTMLHeader[$this->page][$OE]['html'] = $this->HTMLHeader['html'];
		}
		if ($this->forcePortraitHeaders && $this->CurOrientation == 'L' && $this->CurOrientation != $this->DefOrientation) {
			$this->saveHTMLHeader[$this->page][$OE]['rotate'] = true;
			$this->saveHTMLHeader[$this->page][$OE]['ml'] = $this->tMargin;
			$this->saveHTMLHeader[$this->page][$OE]['mr'] = $this->bMargin;
			$this->saveHTMLHeader[$this->page][$OE]['mh'] = $this->margin_header;
			$this->saveHTMLHeader[$this->page][$OE]['mf'] = $this->margin_footer;
			$this->saveHTMLHeader[$this->page][$OE]['pw'] = $this->h;
			$this->saveHTMLHeader[$this->page][$OE]['ph'] = $this->w;
		} else {
			$this->saveHTMLHeader[$this->page][$OE]['ml'] = $this->lMargin;
			$this->saveHTMLHeader[$this->page][$OE]['mr'] = $this->rMargin;
			$this->saveHTMLHeader[$this->page][$OE]['mh'] = $this->margin_header;
			$this->saveHTMLHeader[$this->page][$OE]['mf'] = $this->margin_footer;
			$this->saveHTMLHeader[$this->page][$OE]['pw'] = $this->w;
			$this->saveHTMLHeader[$this->page][$OE]['ph'] = $this->h;
		}
	}

	function writeHTMLFooters()
	{

		if ($this->mirrorMargins && ($this->page) % 2 == 0) {
			$OE = 'E';
		} // EVEN
		else {
			$OE = 'O';
		}
		if ($OE == 'E') {
			$this->saveHTMLFooter[$this->page][$OE]['html'] = $this->HTMLFooterE['html'];
		} else {
			$this->saveHTMLFooter[$this->page][$OE]['html'] = $this->HTMLFooter['html'];
		}
		if ($this->forcePortraitHeaders && $this->CurOrientation == 'L' && $this->CurOrientation != $this->DefOrientation) {
			$this->saveHTMLFooter[$this->page][$OE]['rotate'] = true;
			$this->saveHTMLFooter[$this->page][$OE]['ml'] = $this->tMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mr'] = $this->bMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mt'] = $this->rMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mb'] = $this->lMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mh'] = $this->margin_header;
			$this->saveHTMLFooter[$this->page][$OE]['mf'] = $this->margin_footer;
			$this->saveHTMLFooter[$this->page][$OE]['pw'] = $this->h;
			$this->saveHTMLFooter[$this->page][$OE]['ph'] = $this->w;
		} else {
			$this->saveHTMLFooter[$this->page][$OE]['ml'] = $this->lMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mr'] = $this->rMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mt'] = $this->tMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mb'] = $this->bMargin;
			$this->saveHTMLFooter[$this->page][$OE]['mh'] = $this->margin_header;
			$this->saveHTMLFooter[$this->page][$OE]['mf'] = $this->margin_footer;
			$this->saveHTMLFooter[$this->page][$OE]['pw'] = $this->w;
			$this->saveHTMLFooter[$this->page][$OE]['ph'] = $this->h;
		}
	}

// mPDF 6
	function _shareHeaderFooterWidth($cl, $cc, $cr)
	{ // mPDF 6
		$l = mb_strlen($cl, 'UTF-8');
		$c = mb_strlen($cc, 'UTF-8');
		$r = mb_strlen($cr, 'UTF-8');
		$s = max($l, $r);
		$tw = $c + 2 * $s;
		if ($tw > 0) {
			return array(intval($s * 100 / $tw), intval($c * 100 / $tw), intval($s * 100 / $tw));
		} else {
			return array(33, 33, 33);
		}
	}

// mPDF 6
// Create an HTML header/footer from array (non-HTML header/footer)
	function _createHTMLheaderFooter($arr, $hf)
	{
		$lContent = (isset($arr['L']['content']) ? $arr['L']['content'] : '');
		$cContent = (isset($arr['C']['content']) ? $arr['C']['content'] : '');
		$rContent = (isset($arr['R']['content']) ? $arr['R']['content'] : '');
		list($lw, $cw, $rw) = $this->_shareHeaderFooterWidth($lContent, $cContent, $rContent);
		if ($hf == 'H') {
			$valign = 'bottom';
			$vpadding = '0 0 ' . $this->header_line_spacing . 'em 0';
		} else {
			$valign = 'top';
			$vpadding = '' . $this->footer_line_spacing . 'em 0 0 0';
		}
		if ($this->directionality == 'rtl') { // table columns get reversed so need different text-alignment
			$talignL = 'right';
			$talignR = 'left';
		} else {
			$talignL = 'left';
			$talignR = 'right';
		}
		$html = '<table width="100%" style="border-collapse: collapse; margin: 0; vertical-align: ' . $valign . '; color: #000000; ';
		if (isset($arr['line']) && $arr['line']) {
			$html .= ' border-' . $valign . ': 0.1mm solid #000000;';
		}
		$html .= '">';
		$html .= '<tr>';
		$html .= '<td width="' . $lw . '%" style="padding: ' . $vpadding . '; text-align: ' . $talignL . '; ';
		if (isset($arr['L']['font-family'])) {
			$html .= ' font-family: ' . $arr['L']['font-family'] . ';';
		}
		if (isset($arr['L']['color'])) {
			$html .= ' color: ' . $arr['L']['color'] . ';';
		}
		if (isset($arr['L']['font-size'])) {
			$html .= ' font-size: ' . $arr['L']['font-size'] . 'pt;';
		}
		if (isset($arr['L']['font-style'])) {
			if ($arr['L']['font-style'] == 'B' || $arr['L']['font-style'] == 'BI') {
				$html .= ' font-weight: bold;';
			}
			if ($arr['L']['font-style'] == 'I' || $arr['L']['font-style'] == 'BI') {
				$html .= ' font-style: italic;';
			}
		}
		$html .= '">' . $lContent . '</td>';
		$html .= '<td width="' . $cw . '%" style="padding: ' . $vpadding . '; text-align: center; ';
		if (isset($arr['C']['font-family'])) {
			$html .= ' font-family: ' . $arr['C']['font-family'] . ';';
		}
		if (isset($arr['C']['color'])) {
			$html .= ' color: ' . $arr['C']['color'] . ';';
		}
		if (isset($arr['C']['font-size'])) {
			$html .= ' font-size: ' . $arr['L']['font-size'] . 'pt;';
		}
		if (isset($arr['C']['font-style'])) {
			if ($arr['C']['font-style'] == 'B' || $arr['C']['font-style'] == 'BI') {
				$html .= ' font-weight: bold;';
			}
			if ($arr['C']['font-style'] == 'I' || $arr['C']['font-style'] == 'BI') {
				$html .= ' font-style: italic;';
			}
		}
		$html .= '">' . $cContent . '</td>';
		$html .= '<td width="' . $rw . '%" style="padding: ' . $vpadding . '; text-align: ' . $talignR . '; ';
		if (isset($arr['R']['font-family'])) {
			$html .= ' font-family: ' . $arr['R']['font-family'] . ';';
		}
		if (isset($arr['R']['color'])) {
			$html .= ' color: ' . $arr['R']['color'] . ';';
		}
		if (isset($arr['R']['font-size'])) {
			$html .= ' font-size: ' . $arr['R']['font-size'] . 'pt;';
		}
		if (isset($arr['R']['font-style'])) {
			if ($arr['R']['font-style'] == 'B' || $arr['R']['font-style'] == 'BI') {
				$html .= ' font-weight: bold;';
			}
			if ($arr['R']['font-style'] == 'I' || $arr['R']['font-style'] == 'BI') {
				$html .= ' font-style: italic;';
			}
		}
		$html .= '">' . $rContent . '</td>';
		$html .= '</tr></table>';
		return $html;
	}

	function DefHeaderByName($name, $arr)
	{
		if (!$name) {
			$name = '_nonhtmldefault';
		}
		$html = $this->_createHTMLheaderFooter($arr, 'H');

		$this->pageHTMLheaders[$name]['html'] = $html;
		$this->pageHTMLheaders[$name]['h'] = $this->_gethtmlheight($html);
	}

	function DefFooterByName($name, $arr)
	{
		if (!$name) {
			$name = '_nonhtmldefault';
		}
		$html = $this->_createHTMLheaderFooter($arr, 'F');

		$this->pageHTMLfooters[$name]['html'] = $html;
		$this->pageHTMLfooters[$name]['h'] = $this->_gethtmlheight($html);
	}

	function SetHeaderByName($name, $side = 'O', $write = false)
	{
		if (!$name) {
			$name = '_nonhtmldefault';
		}
		$this->SetHTMLHeader($this->pageHTMLheaders[$name], $side, $write);
	}

	function SetFooterByName($name, $side = 'O')
	{
		if (!$name) {
			$name = '_nonhtmldefault';
		}
		$this->SetHTMLFooter($this->pageHTMLfooters[$name], $side);
	}

	function DefHTMLHeaderByName($name, $html)
	{
		if (!$name) {
			$name = '_default';
		}

		$this->pageHTMLheaders[$name]['html'] = $html;
		$this->pageHTMLheaders[$name]['h'] = $this->_gethtmlheight($html);
	}

	function DefHTMLFooterByName($name, $html)
	{
		if (!$name) {
			$name = '_default';
		}

		$this->pageHTMLfooters[$name]['html'] = $html;
		$this->pageHTMLfooters[$name]['h'] = $this->_gethtmlheight($html);
	}

	function SetHTMLHeaderByName($name, $side = 'O', $write = false)
	{
		if (!$name) {
			$name = '_default';
		}
		$this->SetHTMLHeader($this->pageHTMLheaders[$name], $side, $write);
	}

	function SetHTMLFooterByName($name, $side = 'O')
	{
		if (!$name) {
			$name = '_default';
		}
		$this->SetHTMLFooter($this->pageHTMLfooters[$name], $side);
	}

	function SetHeader($Harray = array(), $side = '', $write = false)
	{
		$oddhtml = '';
		$evenhtml = '';
		if (is_string($Harray)) {
			if (strlen($Harray) == 0) {
				$oddhtml = '';
				$evenhtml = '';
			} else if (strpos($Harray, '|') !== false) {
				$hdet = explode('|', $Harray);
				list($lw, $cw, $rw) = $this->_shareHeaderFooterWidth($hdet[0], $hdet[1], $hdet[2]);
				$oddhtml = '<table width="100%" style="border-collapse: collapse; margin: 0; vertical-align: bottom; color: #000000; ';
				if ($this->defaultheaderfontsize) {
					$oddhtml .= ' font-size: ' . $this->defaultheaderfontsize . 'pt;';
				}
				if ($this->defaultheaderfontstyle) {
					if ($this->defaultheaderfontstyle == 'B' || $this->defaultheaderfontstyle == 'BI') {
						$oddhtml .= ' font-weight: bold;';
					}
					if ($this->defaultheaderfontstyle == 'I' || $this->defaultheaderfontstyle == 'BI') {
						$oddhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultheaderline) {
					$oddhtml .= ' border-bottom: 0.1mm solid #000000;';
				}
				$oddhtml .= '">';
				$oddhtml .= '<tr>';
				$oddhtml .= '<td width="' . $lw . '%" style="padding: 0 0 ' . $this->header_line_spacing . 'em 0; text-align: left; ">' . $hdet[0] . '</td>';
				$oddhtml .= '<td width="' . $cw . '%" style="padding: 0 0 ' . $this->header_line_spacing . 'em 0; text-align: center; ">' . $hdet[1] . '</td>';
				$oddhtml .= '<td width="' . $rw . '%" style="padding: 0 0 ' . $this->header_line_spacing . 'em 0; text-align: right; ">' . $hdet[2] . '</td>';
				$oddhtml .= '</tr></table>';

				$evenhtml = '<table width="100%" style="border-collapse: collapse; margin: 0; vertical-align: bottom; color: #000000; ';
				if ($this->defaultheaderfontsize) {
					$evenhtml .= ' font-size: ' . $this->defaultheaderfontsize . 'pt;';
				}
				if ($this->defaultheaderfontstyle) {
					if ($this->defaultheaderfontstyle == 'B' || $this->defaultheaderfontstyle == 'BI') {
						$evenhtml .= ' font-weight: bold;';
					}
					if ($this->defaultheaderfontstyle == 'I' || $this->defaultheaderfontstyle == 'BI') {
						$evenhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultheaderline) {
					$evenhtml .= ' border-bottom: 0.1mm solid #000000;';
				}
				$evenhtml .= '">';
				$evenhtml .= '<tr>';
				$evenhtml .= '<td width="' . $rw . '%" style="padding: 0 0 ' . $this->header_line_spacing . 'em 0; text-align: left; ">' . $hdet[2] . '</td>';
				$evenhtml .= '<td width="' . $cw . '%" style="padding: 0 0 ' . $this->header_line_spacing . 'em 0; text-align: center; ">' . $hdet[1] . '</td>';
				$evenhtml .= '<td width="' . $lw . '%" style="padding: 0 0 ' . $this->header_line_spacing . 'em 0; text-align: right; ">' . $hdet[0] . '</td>';
				$evenhtml .= '</tr></table>';
			} else {
				$oddhtml = '<div style="margin: 0; color: #000000; ';
				if ($this->defaultheaderfontsize) {
					$oddhtml .= ' font-size: ' . $this->defaultheaderfontsize . 'pt;';
				}
				if ($this->defaultheaderfontstyle) {
					if ($this->defaultheaderfontstyle == 'B' || $this->defaultheaderfontstyle == 'BI') {
						$oddhtml .= ' font-weight: bold;';
					}
					if ($this->defaultheaderfontstyle == 'I' || $this->defaultheaderfontstyle == 'BI') {
						$oddhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultheaderline) {
					$oddhtml .= ' border-bottom: 0.1mm solid #000000;';
				}
				$oddhtml .= 'text-align: right; ">' . $Harray . '</div>';

				$evenhtml = '<div style="margin: 0; color: #000000; ';
				if ($this->defaultheaderfontsize) {
					$evenhtml .= ' font-size: ' . $this->defaultheaderfontsize . 'pt;';
				}
				if ($this->defaultheaderfontstyle) {
					if ($this->defaultheaderfontstyle == 'B' || $this->defaultheaderfontstyle == 'BI') {
						$evenhtml .= ' font-weight: bold;';
					}
					if ($this->defaultheaderfontstyle == 'I' || $this->defaultheaderfontstyle == 'BI') {
						$evenhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultheaderline) {
					$evenhtml .= ' border-bottom: 0.1mm solid #000000;';
				}
				$evenhtml .= 'text-align: left; ">' . $Harray . '</div>';
			}
		} else if (is_array($Harray) && !empty($Harray)) {
			if ($side == 'O') {
				$odd = $Harray;
			} else if ($side == 'E') {
				$even = $Harray;
			} else {
				$odd = $Harray['odd'];
				$even = $Harray['even'];
			}
			$oddhtml = $this->_createHTMLheaderFooter($odd, 'H');

			$evenhtml = $this->_createHTMLheaderFooter($even, 'H');
		}

		if ($side == 'E') {
			$this->SetHTMLHeader($evenhtml, 'E', $write);
		} else if ($side == 'O') {
			$this->SetHTMLHeader($oddhtml, 'O', $write);
		} else {
			$this->SetHTMLHeader($oddhtml, 'O', $write);
			$this->SetHTMLHeader($evenhtml, 'E', $write);
		}
	}

	function SetFooter($Farray = array(), $side = '')
	{
		$oddhtml = '';
		$evenhtml = '';
		if (is_string($Farray)) {
			if (strlen($Farray) == 0) {
				$oddhtml = '';
				$evenhtml = '';
			} else if (strpos($Farray, '|') !== false) {
				$hdet = explode('|', $Farray);
				$oddhtml = '<table width="100%" style="border-collapse: collapse; margin: 0; vertical-align: top; color: #000000; ';
				if ($this->defaultfooterfontsize) {
					$oddhtml .= ' font-size: ' . $this->defaultfooterfontsize . 'pt;';
				}
				if ($this->defaultfooterfontstyle) {
					if ($this->defaultfooterfontstyle == 'B' || $this->defaultfooterfontstyle == 'BI') {
						$oddhtml .= ' font-weight: bold;';
					}
					if ($this->defaultfooterfontstyle == 'I' || $this->defaultfooterfontstyle == 'BI') {
						$oddhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultfooterline) {
					$oddhtml .= ' border-top: 0.1mm solid #000000;';
				}
				$oddhtml .= '">';
				$oddhtml .= '<tr>';
				$oddhtml .= '<td width="33%" style="padding: ' . $this->footer_line_spacing . 'em 0 0 0; text-align: left; ">' . $hdet[0] . '</td>';
				$oddhtml .= '<td width="33%" style="padding: ' . $this->footer_line_spacing . 'em 0 0 0; text-align: center; ">' . $hdet[1] . '</td>';
				$oddhtml .= '<td width="33%" style="padding: ' . $this->footer_line_spacing . 'em 0 0 0; text-align: right; ">' . $hdet[2] . '</td>';
				$oddhtml .= '</tr></table>';

				$evenhtml = '<table width="100%" style="border-collapse: collapse; margin: 0; vertical-align: top; color: #000000; ';
				if ($this->defaultfooterfontsize) {
					$evenhtml .= ' font-size: ' . $this->defaultfooterfontsize . 'pt;';
				}
				if ($this->defaultfooterfontstyle) {
					if ($this->defaultfooterfontstyle == 'B' || $this->defaultfooterfontstyle == 'BI') {
						$evenhtml .= ' font-weight: bold;';
					}
					if ($this->defaultfooterfontstyle == 'I' || $this->defaultfooterfontstyle == 'BI') {
						$evenhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultfooterline) {
					$evenhtml .= ' border-top: 0.1mm solid #000000;';
				}
				$evenhtml .= '">';
				$evenhtml .= '<tr>';
				$evenhtml .= '<td width="33%" style="padding: ' . $this->footer_line_spacing . 'em 0 0 0; text-align: left; ">' . $hdet[2] . '</td>';
				$evenhtml .= '<td width="33%" style="padding: ' . $this->footer_line_spacing . 'em 0 0 0; text-align: center; ">' . $hdet[1] . '</td>';
				$evenhtml .= '<td width="33%" style="padding: ' . $this->footer_line_spacing . 'em 0 0 0; text-align: right; ">' . $hdet[0] . '</td>';
				$evenhtml .= '</tr></table>';
			} else {
				$oddhtml = '<div style="margin: 0; color: #000000; ';
				if ($this->defaultfooterfontsize) {
					$oddhtml .= ' font-size: ' . $this->defaultfooterfontsize . 'pt;';
				}
				if ($this->defaultfooterfontstyle) {
					if ($this->defaultfooterfontstyle == 'B' || $this->defaultfooterfontstyle == 'BI') {
						$oddhtml .= ' font-weight: bold;';
					}
					if ($this->defaultfooterfontstyle == 'I' || $this->defaultfooterfontstyle == 'BI') {
						$oddhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultfooterline) {
					$oddhtml .= ' border-top: 0.1mm solid #000000;';
				}
				$oddhtml .= 'text-align: right; ">' . $Farray . '</div>';

				$evenhtml = '<div style="margin: 0; color: #000000; ';
				if ($this->defaultfooterfontsize) {
					$evenhtml .= ' font-size: ' . $this->defaultfooterfontsize . 'pt;';
				}
				if ($this->defaultfooterfontstyle) {
					if ($this->defaultfooterfontstyle == 'B' || $this->defaultfooterfontstyle == 'BI') {
						$evenhtml .= ' font-weight: bold;';
					}
					if ($this->defaultfooterfontstyle == 'I' || $this->defaultfooterfontstyle == 'BI') {
						$evenhtml .= ' font-style: italic;';
					}
				}
				if ($this->defaultfooterline) {
					$evenhtml .= ' border-top: 0.1mm solid #000000;';
				}
				$evenhtml .= 'text-align: left; ">' . $Farray . '</div>';
			}
		} else if (is_array($Farray)) {
			if ($side == 'O') {
				$odd = $Farray;
			} else if ($side == 'E') {
				$even = $Farray;
			} else {
				if (isset($Farray['odd']))
					$odd = $Farray['odd'];
				if (isset($Farray['even']))
					$even = $Farray['even'];
			}

			if (isset($odd))
				$oddhtml = $this->_createHTMLheaderFooter($odd, 'F');

			if (isset($even))
				$evenhtml = $this->_createHTMLheaderFooter($even, 'F');
		}
		/* -- HTMLfooterS-FOOTERS -- */
		if ($side == 'E') {
			$this->SetHTMLfooter($evenhtml, 'E');
		} else if ($side == 'O') {
			$this->SetHTMLfooter($oddhtml, 'O');
		} else {
			$this->SetHTMLfooter($oddhtml, 'O');
			$this->SetHTMLfooter($evenhtml, 'E');
		}
		/* -- END HTMLfooterS-FOOTERS -- */
	}

	
//Page footer
	function Footer()
	{
		/* -- CSS-PAGE -- */
		// PAGED MEDIA - CROP / CROSS MARKS from @PAGE
		if ($this->show_marks == 'CROP' || $this->show_marks == 'CROPCROSS') {
			// Show TICK MARKS
			$this->SetLineWidth(0.1); // = 0.1 mm
			$this->SetDColor($this->ConvertColor(0));
			$l = $this->cropMarkLength;
			$m = $this->cropMarkMargin; // Distance of crop mark from margin
			$b = $this->nonPrintMargin; // Non-printable border at edge of paper sheet
			$ax1 = $b;
			$bx = $this->page_box['outer_width_LR'] - $m;
			$ax = max($ax1, $bx - $l);
			$cx1 = $this->w - $b;
			$dx = $this->w - $this->page_box['outer_width_LR'] + $m;
			$cx = min($cx1, $dx + $l);
			$ay1 = $b;
			$by = $this->page_box['outer_width_TB'] - $m;
			$ay = max($ay1, $by - $l);
			$cy1 = $this->h - $b;
			$dy = $this->h - $this->page_box['outer_width_TB'] + $m;
			$cy = min($cy1, $dy + $l);

			$this->Line($ax, $this->page_box['outer_width_TB'], $bx, $this->page_box['outer_width_TB']);
			$this->Line($cx, $this->page_box['outer_width_TB'], $dx, $this->page_box['outer_width_TB']);
			$this->Line($ax, $this->h - $this->page_box['outer_width_TB'], $bx, $this->h - $this->page_box['outer_width_TB']);
			$this->Line($cx, $this->h - $this->page_box['outer_width_TB'], $dx, $this->h - $this->page_box['outer_width_TB']);
			$this->Line($this->page_box['outer_width_LR'], $ay, $this->page_box['outer_width_LR'], $by);
			$this->Line($this->page_box['outer_width_LR'], $cy, $this->page_box['outer_width_LR'], $dy);
			$this->Line($this->w - $this->page_box['outer_width_LR'], $ay, $this->w - $this->page_box['outer_width_LR'], $by);
			$this->Line($this->w - $this->page_box['outer_width_LR'], $cy, $this->w - $this->page_box['outer_width_LR'], $dy);

			if ($this->printers_info) {
				$hd = date('Y-m-d H:i') . '  Page ' . $this->page . ' of {nb}';
				$this->SetTColor($this->ConvertColor(0));
				$this->SetFont('arial', '', 7.5, true, true);
				$this->x = $this->page_box['outer_width_LR'] + 1.5;
				$this->y = 1;
				$this->Cell($headerpgwidth, $this->FontSize, $hd, 0, 0, 'L', 0, '', 0, 0, 0, 'M');
				$this->SetFont($this->default_font, '', $this->original_default_font_size);
			}
		}
		if ($this->show_marks == 'CROSS' || $this->show_marks == 'CROPCROSS') {
			$this->SetLineWidth(0.1); // = 0.1 mm
			$this->SetDColor($this->ConvertColor(0));
			$l = 14 / 2; // longer length of the cross line (half)
			$w = 6 / 2; // shorter width of the cross line (half)
			$r = 1.2; // radius of circle
			$m = $this->crossMarkMargin; // Distance of cross mark from margin
			$x1 = $this->page_box['outer_width_LR'] - $m;
			$x2 = $this->w - $this->page_box['outer_width_LR'] + $m;
			$y1 = $this->page_box['outer_width_TB'] - $m;
			$y2 = $this->h - $this->page_box['outer_width_TB'] + $m;
			// Left
			$this->Circle($x1, $this->h / 2, $r, 'S');
			$this->Line($x1 - $w, $this->h / 2, $x1 + $w, $this->h / 2);
			$this->Line($x1, $this->h / 2 - $l, $x1, $this->h / 2 + $l);
			// Right
			$this->Circle($x2, $this->h / 2, $r, 'S');
			$this->Line($x2 - $w, $this->h / 2, $x2 + $w, $this->h / 2);
			$this->Line($x2, $this->h / 2 - $l, $x2, $this->h / 2 + $l);
			// Top
			$this->Circle($this->w / 2, $y1, $r, 'S');
			$this->Line($this->w / 2, $y1 - $w, $this->w / 2, $y1 + $w);
			$this->Line($this->w / 2 - $l, $y1, $this->w / 2 + $l, $y1);
			// Bottom
			$this->Circle($this->w / 2, $y2, $r, 'S');
			$this->Line($this->w / 2, $y2 - $w, $this->w / 2, $y2 + $w);
			$this->Line($this->w / 2 - $l, $y2, $this->w / 2 + $l, $y2);
		}

		/* -- END CSS-PAGE -- */

		// mPDF 6
		// If @page set non-HTML headers/footers named, they were not read until later in the HTML code - so now set them
		if ($this->page == 1) {
			if ($this->firstPageBoxHeader) {
				if (isset($this->pageHTMLheaders[$this->firstPageBoxHeader])) {
					$this->HTMLHeader = $this->pageHTMLheaders[$this->firstPageBoxHeader];
				}
				$this->Header();
			}
			if ($this->firstPageBoxFooter) {
				if (isset($this->pageHTMLfooters[$this->firstPageBoxFooter])) {
					$this->HTMLFooter = $this->pageHTMLfooters[$this->firstPageBoxFooter];
				}
			}
			$this->firstPageBoxHeader = '';
			$this->firstPageBoxFooter = '';
		}


		if (($this->mirrorMargins && ($this->page % 2 == 0) && $this->HTMLFooterE) || ($this->mirrorMargins && ($this->page % 2 == 1) && $this->HTMLFooter) || (!$this->mirrorMargins && $this->HTMLFooter)) {
			$this->writeHTMLFooters();
		}

		/* -- WATERMARK -- */
		if (($this->watermarkText) && ($this->showWatermarkText)) {
			$this->watermark($this->watermarkText, 45, 120, $this->watermarkTextAlpha); // Watermark text
		}
		if (($this->watermarkImage) && ($this->showWatermarkImage)) {
			$this->watermarkImg($this->watermarkImage, $this->watermarkImageAlpha); // Watermark image
		}
		/* -- END WATERMARK -- */
	}

	


	/* -- HTML-CSS -- */

///////////////////
/// HTML parser ///
///////////////////
	function WriteHTML($html, $sub = 0, $init = true, $close = true)
	{
		// $sub - 0 = default; 1=headerCSS only; 2=HTML body (parts) only; 3 - HTML parses only
		// 4 - writes HTML headers/Fixed pos DIVs - stores in buffer - for single page only
		// $close - if false Leaves buffers etc. in current state, so that it can continue a block etc.
		// $init - Clears and sets buffers to Top level block etc.

		if (empty($html)) {
			$html = '';
		}
		if ($this->progressBar) {
			$this->UpdateProgressBar(1, 0, 'Parsing CSS & Headers');
		} // *PROGRESS-BAR*

		if ($init) {
			$this->headerbuffer = '';
			$this->textbuffer = array();
			$this->fixedPosBlockSave = array();
		}
		if ($sub == 1) {
			$html = '<style> ' . $html . ' </style>';
		} // stylesheet only

		if ($this->allow_charset_conversion) {
			if ($sub < 1) {
				$this->ReadCharset($html);
			}
			if ($this->charset_in && $sub != 4) {
				$success = iconv($this->charset_in, 'UTF-8//TRANSLIT', $html);
				if ($success) {
					$html = $success;
				}
			}
		}
		$html = $this->purify_utf8($html, false);
		if ($init) {
			$this->blklvl = 0;
			$this->lastblocklevelchange = 0;
			$this->blk = array();
			$this->initialiseBlock($this->blk[0]);
			$this->blk[0]['width'] = & $this->pgwidth;
			$this->blk[0]['inner_width'] = & $this->pgwidth;
			$this->blk[0]['blockContext'] = $this->blockContext;
		}

		$zproperties = array();
		if ($sub < 2) {
			$this->ReadMetaTags($html);

			if (preg_match('/<base[^>]*href=["\']([^"\'>]*)["\']/i', $html, $m)) {
				$this->SetBasePath($m[1]);
			}
			$html = $this->cssmgr->ReadCSS($html);

			if ($this->autoLangToFont && !$this->usingCoreFont && preg_match('/<html [^>]*lang=[\'\"](.*?)[\'\"]/ism', $html, $m)) {
				$html_lang = $m[1];
			}

			if (preg_match('/<html [^>]*dir=[\'\"]\s*rtl\s*[\'\"]/ism', $html)) {
				$zproperties['DIRECTION'] = 'rtl';
			}

			// allow in-line CSS for body tag to be parsed // Get <body> tag inline CSS
			if (preg_match('/<body([^>]*)>(.*?)<\/body>/ism', $html, $m) || preg_match('/<body([^>]*)>(.*)$/ism', $html, $m)) {
				$html = $m[2];
				// Changed to allow style="background: url('bg.jpg')"
				if (preg_match('/style=[\"](.*?)[\"]/ism', $m[1], $mm) || preg_match('/style=[\'](.*?)[\']/ism', $m[1], $mm)) {
					$zproperties = $this->cssmgr->readInlineCSS($mm[1]);
				}
				if (preg_match('/dir=[\'\"]\s*rtl\s*[\'\"]/ism', $m[1])) {
					$zproperties['DIRECTION'] = 'rtl';
				}
				if (isset($html_lang) && $html_lang) {
					$zproperties['LANG'] = $html_lang;
				}
				if ($this->autoLangToFont && !$this->onlyCoreFonts && preg_match('/lang=[\'\"](.*?)[\'\"]/ism', $m[1], $mm)) {
					$zproperties['LANG'] = $mm[1];
				}
			}
		}
		$properties = $this->cssmgr->MergeCSS('BLOCK', 'BODY', '');
		if ($zproperties) {
			$properties = $this->cssmgr->array_merge_recursive_unique($properties, $zproperties);
		}

		if (isset($properties['DIRECTION']) && $properties['DIRECTION']) {
			$this->cssmgr->CSS['BODY']['DIRECTION'] = $properties['DIRECTION'];
		}
		if (!isset($this->cssmgr->CSS['BODY']['DIRECTION'])) {
			$this->cssmgr->CSS['BODY']['DIRECTION'] = $this->directionality;
		} else {
			$this->SetDirectionality($this->cssmgr->CSS['BODY']['DIRECTION']);
		}

		$this->setCSS($properties, '', 'BODY');

		$this->blk[0]['InlineProperties'] = $this->saveInlineProperties();

		if ($sub == 1) {
			return '';
		}
		if (!isset($this->cssmgr->CSS['BODY'])) {
			$this->cssmgr->CSS['BODY'] = array();
		}

		/* -- BACKGROUNDS -- */
		if (isset($properties['BACKGROUND-GRADIENT'])) {
			$this->bodyBackgroundGradient = $properties['BACKGROUND-GRADIENT'];
		}

		if (isset($properties['BACKGROUND-IMAGE']) && $properties['BACKGROUND-IMAGE']) {
			$ret = $this->SetBackground($properties, $this->pgwidth);
			if ($ret) {
				$this->bodyBackgroundImage = $ret;
			}
		}
		/* -- END BACKGROUNDS -- */

		/* -- CSS-PAGE -- */
		// If page-box is set
		if ($this->state == 0 && ((isset($this->cssmgr->CSS['@PAGE']) && $this->cssmgr->CSS['@PAGE']) || (isset($this->cssmgr->CSS['@PAGE>>PSEUDO>>FIRST']) && $this->cssmgr->CSS['@PAGE>>PSEUDO>>FIRST']))) { // mPDF 5.7.3
			$this->page_box['current'] = '';
			$this->page_box['using'] = true;
			list($pborientation, $pbmgl, $pbmgr, $pbmgt, $pbmgb, $pbmgh, $pbmgf, $hname, $fname, $bg, $resetpagenum, $pagenumstyle, $suppress, $marks, $newformat) = $this->SetPagedMediaCSS('', false, 'O');
			$this->DefOrientation = $this->CurOrientation = $pborientation;
			$this->orig_lMargin = $this->DeflMargin = $pbmgl;
			$this->orig_rMargin = $this->DefrMargin = $pbmgr;
			$this->orig_tMargin = $this->tMargin = $pbmgt;
			$this->orig_bMargin = $this->bMargin = $pbmgb;
			$this->orig_hMargin = $this->margin_header = $pbmgh;
			$this->orig_fMargin = $this->margin_footer = $pbmgf;
			list($pborientation, $pbmgl, $pbmgr, $pbmgt, $pbmgb, $pbmgh, $pbmgf, $hname, $fname, $bg, $resetpagenum, $pagenumstyle, $suppress, $marks, $newformat) = $this->SetPagedMediaCSS('', true, 'O'); // first page
			$this->show_marks = $marks;
			if ($hname)
				$this->firstPageBoxHeader = $hname;
			if ($fname)
				$this->firstPageBoxFooter = $fname;
		}
		/* -- END CSS-PAGE -- */

		$parseonly = false;
		$this->bufferoutput = false;
		if ($sub == 3) {
			$parseonly = true;
			// Close any open block tags
			$arr = array();
			$ai = 0;
			for ($b = $this->blklvl; $b > 0; $b--) {
				$this->CloseTag($this->blk[$b]['tag'], $arr, $ai);
			}
			// Output any text left in buffer
			if (count($this->textbuffer)) {
				$this->printbuffer($this->textbuffer);
			}
			$this->textbuffer = array();
		} else if ($sub == 4) {
			// Close any open block tags
			$arr = array();
			$ai = 0;
			for ($b = $this->blklvl; $b > 0; $b--) {
				$this->CloseTag($this->blk[$b]['tag'], $arr, $ai);
			}
			// Output any text left in buffer
			if (count($this->textbuffer)) {
				$this->printbuffer($this->textbuffer);
			}
			$this->bufferoutput = true;
			$this->textbuffer = array();
			$this->headerbuffer = '';
			$properties = $this->cssmgr->MergeCSS('BLOCK', 'BODY', '');
			$this->setCSS($properties, '', 'BODY');
		}

		mb_internal_encoding('UTF-8');

		$html = $this->AdjustHTML($html, $this->tabSpaces); //Try to make HTML look more like XHTML

		if ($this->autoScriptToLang) {
			$html = $this->markScriptToLang($html);
		}

		preg_match_all('/<htmlpageheader([^>]*)>(.*?)<\/htmlpageheader>/si', $html, $h);
		for ($i = 0; $i < count($h[1]); $i++) {
			if (preg_match('/name=[\'|\"](.*?)[\'|\"]/', $h[1][$i], $n)) {
				$this->pageHTMLheaders[$n[1]]['html'] = $h[2][$i];
				$this->pageHTMLheaders[$n[1]]['h'] = $this->_gethtmlheight($h[2][$i]);
			}
		}
		preg_match_all('/<htmlpagefooter([^>]*)>(.*?)<\/htmlpagefooter>/si', $html, $f);
		for ($i = 0; $i < count($f[1]); $i++) {
			if (preg_match('/name=[\'|\"](.*?)[\'|\"]/', $f[1][$i], $n)) {
				$this->pageHTMLfooters[$n[1]]['html'] = $f[2][$i];
				$this->pageHTMLfooters[$n[1]]['h'] = $this->_gethtmlheight($f[2][$i]);
			}
		}

		$html = preg_replace('/<htmlpageheader.*?<\/htmlpageheader>/si', '', $html);
		$html = preg_replace('/<htmlpagefooter.*?<\/htmlpagefooter>/si', '', $html);

		if ($this->state == 0 && $sub != 1 && $sub != 3 && $sub != 4) {
			$this->AddPage($this->CurOrientation);
		}


		if (isset($hname) && preg_match('/^html_(.*)$/i', $hname, $n))
			$this->SetHTMLHeader($this->pageHTMLheaders[$n[1]], 'O', true);
		if (isset($fname) && preg_match('/^html_(.*)$/i', $fname, $n))
			$this->SetHTMLFooter($this->pageHTMLfooters[$n[1]], 'O');



		$html = str_replace('<?', '< ', $html); //Fix '<?XML' bug from HTML code generated by MS Word

		$this->checkSIP = false;
		$this->checkSMP = false;
		$this->checkCJK = false;
		if ($this->onlyCoreFonts) {
			$html = $this->SubstituteChars($html);
		} else {
			if (preg_match("/([" . $this->pregRTLchars . "])/u", $html)) {
				$this->biDirectional = true;
			} // *OTL*
			if (preg_match("/([\x{20000}-\x{2FFFF}])/u", $html)) {
				$this->checkSIP = true;
			}
			if (preg_match("/([\x{10000}-\x{1FFFF}])/u", $html)) {
				$this->checkSMP = true;
			}
			/* -- CJK-FONTS -- */
			if (preg_match("/([" . $this->pregCJKchars . "])/u", $html)) {
				$this->checkCJK = true;
			}
			/* -- END CJK-FONTS -- */
		}

		// Don't allow non-breaking spaces that are converted to substituted chars or will break anyway and mess up table width calc.
		$html = str_replace('<tta>160</tta>', chr(32), $html);
		$html = str_replace('</tta><tta>', '|', $html);
		$html = str_replace('</tts><tts>', '|', $html);
		$html = str_replace('</ttz><ttz>', '|', $html);

		//Add new supported tags in the DisableTags function
		$html = strip_tags($html, $this->enabledtags); //remove all unsupported tags, but the ones inside the 'enabledtags' string
		//Explode the string in order to parse the HTML code
		$a = preg_split('/<(.*?)>/ms', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
		// ? more accurate regexp that allows e.g. <a name="Silly <name>">
		// if changing - also change in fn.SubstituteChars()
		// $a = preg_split ('/<((?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+)>/ms', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

		if ($this->mb_enc) {
			mb_internal_encoding($this->mb_enc);
		}
		$pbc = 0;
		if ($this->progressBar) {
			$this->UpdateProgressBar(1, 0);
		} // *PROGRESS-BAR*
		$this->subPos = -1;
		$cnt = count($a);
		for ($i = 0; $i < $cnt; $i++) {
			$e = $a[$i];
			if ($i % 2 == 0) {
				//TEXT
				if ($this->blk[$this->blklvl]['hide']) {
					continue;
				}
				if ($this->inlineDisplayOff) {
					continue;
				}
				if ($this->inMeter) {
					continue;
				}

				if ($this->inFixedPosBlock) {
					$this->fixedPosBlock .= $e;
					continue;
				} // *CSS-POSITION*
				if (strlen($e) == 0) {
					continue;
				}

				if ($this->ignorefollowingspaces && !$this->ispre) {
					if (strlen(ltrim($e)) == 0) {
						continue;
					}
					if ($this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats' && substr($e, 0, 1) == ' ') {
						$this->ignorefollowingspaces = false;
						$e = ltrim($e);
					}
				}

				$this->OTLdata = NULL;  // mPDF 5.7.1

				$e = strcode2utf($e);
				$e = $this->lesser_entity_decode($e);

				if ($this->usingCoreFont) {
					// If core font is selected in document which is not onlyCoreFonts - substitute with non-core font
					if ($this->useSubstitutions && !$this->onlyCoreFonts && $this->subPos < $i && !$this->specialcontent) {
						$cnt += $this->SubstituteCharsNonCore($a, $i, $e);
					}
					// CONVERT ENCODING
					$e = mb_convert_encoding($e, $this->mb_enc, 'UTF-8');
					if ($this->textvar & FT_UPPERCASE) {
						$e = mb_strtoupper($e, $this->mb_enc);
					} // mPDF 5.7.1
					else if ($this->textvar & FT_LOWERCASE) {
						$e = mb_strtolower($e, $this->mb_enc);
					} // mPDF 5.7.1
					else if ($this->textvar & FT_CAPITALIZE) {
						$e = mb_convert_case($e, MB_CASE_TITLE, "UTF-8");
					} // mPDF 5.7.1
				} else {
					if ($this->checkSIP && $this->CurrentFont['sipext'] && $this->subPos < $i && (!$this->specialcontent || !$this->useActiveForms)) {
						$cnt += $this->SubstituteCharsSIP($a, $i, $e);
					}

					if ($this->useSubstitutions && !$this->onlyCoreFonts && $this->CurrentFont['type'] != 'Type0' && $this->subPos < $i && (!$this->specialcontent || !$this->useActiveForms)) {
						$cnt += $this->SubstituteCharsMB($a, $i, $e);
					}

					if ($this->textvar & FT_UPPERCASE) {
						$e = mb_strtoupper($e, $this->mb_enc);
					} else if ($this->textvar & FT_LOWERCASE) {
						$e = mb_strtolower($e, $this->mb_enc);
					} else if ($this->textvar & FT_CAPITALIZE) {
						$e = mb_convert_case($e, MB_CASE_TITLE, "UTF-8");
					}

					/* -- OTL -- */
					// Use OTL OpenType Table Layout - GSUB & GPOS
					if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL'] && (!$this->specialcontent || !$this->useActiveForms)) {
						$e = $this->otl->applyOTL($e, $this->CurrentFont['useOTL']);
						$this->OTLdata = $this->otl->OTLdata;
						$this->otl->removeChar($e, $this->OTLdata, "\xef\xbb\xbf"); // Remove ZWNBSP (also Byte order mark FEFF)
					}
					/* -- END OTL -- */ else { // *OTL*
						// removes U+200E/U+200F LTR and RTL mark and U+200C/U+200D Zero-width Joiner and Non-joiner
						$e = preg_replace("/[\xe2\x80\x8c\xe2\x80\x8d\xe2\x80\x8e\xe2\x80\x8f]/u", '', $e);
						$e = preg_replace("/[\xef\xbb\xbf]/u", '', $e); // Remove ZWNBSP (also Byte order mark FEFF)
					} // *OTL*
				}
				if (($this->tts) || ($this->ttz) || ($this->tta)) {
					$es = explode('|', $e);
					$e = '';
					foreach ($es AS $val) {
						$e .= chr($val);
					}
				}

				//  FORM ELEMENTS
				if ($this->specialcontent) {
					/* -- FORMS -- */
					//SELECT tag (form element)
					if ($this->specialcontent == "type=select") {
						$e = ltrim($e);
						if (!empty($this->OTLdata)) {
							$this->otl->trimOTLdata($this->OTLdata, true, false);
						} // *OTL*
						$stringwidth = $this->GetStringWidth($e);
						if (!isset($this->selectoption['MAXWIDTH']) || $stringwidth > $this->selectoption['MAXWIDTH']) {
							$this->selectoption['MAXWIDTH'] = $stringwidth;
						}
						if (!isset($this->selectoption['SELECTED']) || $this->selectoption['SELECTED'] == '') {
							$this->selectoption['SELECTED'] = $e;
							if (!empty($this->OTLdata)) {
								$this->selectoption['SELECTED-OTLDATA'] = $this->OTLdata;
							} // *OTL*
						}
						// Active Forms
						if (isset($this->selectoption['ACTIVE']) && $this->selectoption['ACTIVE']) {
							$this->selectoption['ITEMS'][] = array('exportValue' => $this->selectoption['currentVAL'], 'content' => $e, 'selected' => $this->selectoption['currentSEL']);
						}
						$this->OTLdata = array();
					}
					// TEXTAREA
					else {
						$objattr = unserialize($this->specialcontent);
						$objattr['text'] = $e;
						$objattr['OTLdata'] = $this->OTLdata;
						$this->OTLdata = array();
						$te = "\xbb\xa4\xactype=textarea,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
						if ($this->tdbegin) {
							$this->_saveCellTextBuffer($te, $this->HREF);
						} else {
							$this->_saveTextBuffer($te, $this->HREF);
						}
					}
					/* -- END FORMS -- */
				}

				// TABLE
				else if ($this->tableLevel) {
					/* -- TABLES -- */
					if ($this->tdbegin) {
						if (($this->ignorefollowingspaces) && !$this->ispre) {
							$e = ltrim($e);
							if (!empty($this->OTLdata)) {
								$this->otl->trimOTLdata($this->OTLdata, true, false);
							} // *OTL*
						}
						if ($e || $e === '0') {
							if ($this->blockjustfinished && $this->cell[$this->row][$this->col]['s'] > 0) {
								$this->_saveCellTextBuffer("\n");
								if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
									$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
								} elseif ($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
									$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
								}
								$this->cell[$this->row][$this->col]['s'] = 0; // reset
							}
							$this->blockjustfinished = false;

							if (!isset($this->cell[$this->row][$this->col]['R']) || !$this->cell[$this->row][$this->col]['R']) {
								if (isset($this->cell[$this->row][$this->col]['s'])) {
									$this->cell[$this->row][$this->col]['s'] += $this->GetStringWidth($e, false, $this->OTLdata, $this->textvar);
								} else {
									$this->cell[$this->row][$this->col]['s'] = $this->GetStringWidth($e, false, $this->OTLdata, $this->textvar);
								}
								if (!empty($this->spanborddet)) {
									$this->cell[$this->row][$this->col]['s'] += (isset($this->spanborddet['L']['w']) ? $this->spanborddet['L']['w'] : 0) + (isset($this->spanborddet['R']['w']) ? $this->spanborddet['R']['w'] : 0);
								}
							}
							$this->_saveCellTextBuffer($e, $this->HREF);
							if (substr($this->cell[$this->row][$this->col]['a'], 0, 1) == 'D') {
								$dp = $this->decimal_align[substr($this->cell[$this->row][$this->col]['a'], 0, 2)];
								$s = preg_split('/' . preg_quote($dp, '/') . '/', $e, 2);  // ? needs to be /u if not core
								$s0 = $this->GetStringWidth($s[0], false);
								if (isset($s[1]) && $s[1]) {
									$s1 = $this->GetStringWidth(($s[1] . $dp), false);
								} else
									$s1 = 0;
								if (!isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs0'])) {
									$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs0'] = $s0;
								} else {
									$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs0'] = max($s0, $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs0']);
								}
								if (!isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs1'])) {
									$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs1'] = $s1;
								} else {
									$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs1'] = max($s1, $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['decimal_align'][$this->col]['maxs1']);
								}
							}

							if ($this->tableLevel == 1 && $this->useGraphs) {
								$this->graphs[$this->currentGraphId]['data'][$this->row][$this->col] = $e;
							}
							$this->nestedtablejustfinished = false;
							$this->linebreakjustfinished = false;
						}
					}
					/* -- END TABLES -- */
				}
				// ALL ELSE
				else {
					if ($this->ignorefollowingspaces && !$this->ispre) {
						$e = ltrim($e);
						if (!empty($this->OTLdata)) {
							$this->otl->trimOTLdata($this->OTLdata, true, false);
						} // *OTL*
					}
					if ($e || $e === '0')
						$this->_saveTextBuffer($e, $this->HREF);
				}
				if ($e || $e === '0')
					$this->ignorefollowingspaces = false; // mPDF 6
				if (substr($e, -1, 1) == ' ' && !$this->ispre && $this->FontFamily != 'csymbol' && $this->FontFamily != 'czapfdingbats') {
					$this->ignorefollowingspaces = true;
				}
			} else { // TAG **
				if (isset($e[0]) && $e[0] == '/') {
					/* -- PROGRESS-BAR -- */
					if ($this->progressBar) {  // 10% increments
						if (intval($i * 10 / $cnt) != $pbc) {
							$pbc = intval($i * 10 / $cnt);
							$this->UpdateProgressBar(1, $pbc * 10, $tag);
						}
					}
					/* -- END PROGRESS-BAR -- */

					$endtag = trim(strtoupper(substr($e, 1)));


					/* -- CSS-POSITION -- */
					// mPDF 6
					if ($this->inFixedPosBlock) {
						if (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags)) {
							$this->fixedPosBlockDepth--;
						}
						if ($this->fixedPosBlockDepth == 0) {
							$this->fixedPosBlockSave[] = array($this->fixedPosBlock, $this->fixedPosBlockBBox, $this->page);
							$this->fixedPosBlock = '';
							$this->inFixedPosBlock = false;
							continue;
						}
						$this->fixedPosBlock .= '<' . $e . '>';
						continue;
					}
					/* -- END CSS-POSITION -- */

					// mPDF 6
					// Correct for tags where HTML5 specifies optional end tags (see also OpenTag() )
					if ($this->allow_html_optional_endtags && !$parseonly) {
						if (isset($this->blk[$this->blklvl]['tag'])) {
							$closed = false;
							// li end tag may be omitted if there is no more content in the parent element
							if (!$closed && $this->blk[$this->blklvl]['tag'] == 'LI' && $endtag != 'LI' && (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags))) {
								$this->CloseTag('LI', $a, $i);
								$closed = true;
							}
							// dd end tag may be omitted if there is no more content in the parent element
							if (!$closed && $this->blk[$this->blklvl]['tag'] == 'DD' && $endtag != 'DD' && (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags))) {
								$this->CloseTag('DD', $a, $i);
								$closed = true;
							}
							// p end tag may be omitted if there is no more content in the parent element and the parent element is not an A element [??????]
							if (!$closed && $this->blk[$this->blklvl]['tag'] == 'P' && $endtag != 'P' && (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags))) {
								$this->CloseTag('P', $a, $i);
								$closed = true;
							}
							// option end tag may be omitted if there is no more content in the parent element
							if (!$closed && $this->blk[$this->blklvl]['tag'] == 'OPTION' && $endtag != 'OPTION' && (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags))) {
								$this->CloseTag('OPTION', $a, $i);
								$closed = true;
							}
						}
						/* -- TABLES -- */
						// Check for Table tags where HTML specifies optional end tags,
						if ($endtag == 'TABLE') {
							if ($this->lastoptionaltag == 'THEAD' || $this->lastoptionaltag == 'TBODY' || $this->lastoptionaltag == 'TFOOT') {
								$this->CloseTag($this->lastoptionaltag, $a, $i);
							}
							if ($this->lastoptionaltag == 'TR') {
								$this->CloseTag('TR', $a, $i);
							}
							if ($this->lastoptionaltag == 'TD' || $this->lastoptionaltag == 'TH') {
								$this->CloseTag($this->lastoptionaltag, $a, $i);
								$this->CloseTag('TR', $a, $i);
							}
						}
						if ($endtag == 'THEAD' || $endtag == 'TBODY' || $endtag == 'TFOOT') {
							if ($this->lastoptionaltag == 'TR') {
								$this->CloseTag('TR', $a, $i);
							}
							if ($this->lastoptionaltag == 'TD' || $this->lastoptionaltag == 'TH') {
								$this->CloseTag($this->lastoptionaltag, $a, $i);
								$this->CloseTag('TR', $a, $i);
							}
						}
						if ($endtag == 'TR') {
							if ($this->lastoptionaltag == 'TD' || $this->lastoptionaltag == 'TH') {
								$this->CloseTag($this->lastoptionaltag, $a, $i);
							}
						}
						/* -- END TABLES -- */
					}


					// mPDF 6
					if ($this->blk[$this->blklvl]['hide']) {
						if (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags)) {
							unset($this->blk[$this->blklvl]);
							$this->blklvl--;
						}
						continue;
					}

					// mPDF 6
					$this->CloseTag($endtag, $a, $i); // mPDF 6
				} else { // OPENING TAG
					if ($this->blk[$this->blklvl]['hide']) {
						if (strpos($e, ' ')) {
							$te = strtoupper(substr($e, 0, strpos($e, ' ')));
						} else {
							$te = strtoupper($e);
						}
						// mPDF 6
						if ($te == 'THEAD' || $te == 'TBODY' || $te == 'TFOOT' || $te == 'TR' || $te == 'TD' || $te == 'TH') {
							$this->lastoptionaltag = $te;
						}
						if (in_array($te, $this->outerblocktags) || in_array($te, $this->innerblocktags)) {
							$this->blklvl++;
							$this->blk[$this->blklvl]['hide'] = true;
							$this->blk[$this->blklvl]['tag'] = $te; // mPDF 6
						}
						continue;
					}

					/* -- CSS-POSITION -- */
					if ($this->inFixedPosBlock) {
						if (strpos($e, ' ')) {
							$te = strtoupper(substr($e, 0, strpos($e, ' ')));
						} else {
							$te = strtoupper($e);
						}
						$this->fixedPosBlock .= '<' . $e . '>';
						if (in_array($te, $this->outerblocktags) || in_array($te, $this->innerblocktags)) {
							$this->fixedPosBlockDepth++;
						}
						continue;
					}
					/* -- END CSS-POSITION -- */
					$regexp = '|=\'(.*?)\'|s'; // eliminate single quotes, if any
					$e = preg_replace($regexp, "=\"\$1\"", $e);
					// changes anykey=anyvalue to anykey="anyvalue" (only do this inside [some] tags)
					if (substr($e, 0, 10) != 'pageheader' && substr($e, 0, 10) != 'pagefooter' && substr($e, 0, 12) != 'tocpagebreak' && substr($e, 0, 10) != 'indexentry' && substr($e, 0, 8) != 'tocentry') { // mPDF 6  (ZZZ99H)
						$regexp = '| (\\w+?)=([^\\s>"]+)|si';
						$e = preg_replace($regexp, " \$1=\"\$2\"", $e);
					}

					$e = preg_replace('/ (\\S+?)\s*=\s*"/i', " \\1=\"", $e);

					//Fix path values, if needed
					$orig_srcpath = '';
					if ((stristr($e, "href=") !== false) or ( stristr($e, "src=") !== false)) {
						$regexp = '/ (href|src)\s*=\s*"(.*?)"/i';
						preg_match($regexp, $e, $auxiliararray);
						if (isset($auxiliararray[2])) {
							$path = $auxiliararray[2];
						} else {
							$path = '';
						}
						if (trim($path) != '' && !(stristr($e, "src=") !== false && substr($path, 0, 4) == 'var:') && substr($path, 0, 1) != '@') {
							$path = htmlspecialchars_decode($path); // mPDF 5.7.4 URLs
							$orig_srcpath = $path;
							$this->GetFullPath($path);
							$regexp = '/ (href|src)="(.*?)"/i';
							$e = preg_replace($regexp, ' \\1="' . $path . '"', $e);
						}
					}//END of Fix path values
					//Extract attributes
					$contents = array();
					$contents1 = array();
					$contents2 = array();
					// Changed to allow style="background: url('bg.jpg')"
					// Changed to improve performance; maximum length of \S (attribute) = 16
					// Increase allowed attribute name to 32 - cutting off "toc-even-header-name" etc.
					preg_match_all('/\\S{1,32}=["][^"]*["]/', $e, $contents1);
					preg_match_all('/\\S{1,32}=[\'][^\']*[\']/i', $e, $contents2);

					$contents = array_merge($contents1, $contents2);
					preg_match('/\\S+/', $e, $a2);
					$tag = (isset($a2[0]) ? strtoupper($a2[0]) : '');
					$attr = array();
					if ($orig_srcpath) {
						$attr['ORIG_SRC'] = $orig_srcpath;
					}
					if (!empty($contents)) {
						foreach ($contents[0] as $v) {
							// Changed to allow style="background: url('bg.jpg')"
							if (preg_match('/^([^=]*)=["]?([^"]*)["]?$/', $v, $a3) || preg_match('/^([^=]*)=[\']?([^\']*)[\']?$/', $v, $a3)) {
								if (strtoupper($a3[1]) == 'ID' || strtoupper($a3[1]) == 'CLASS') { // 4.2.013 Omits STYLE
									$attr[strtoupper($a3[1])] = trim(strtoupper($a3[2]));
								}
								// includes header-style-right etc. used for <pageheader>
								else if (preg_match('/^(HEADER|FOOTER)-STYLE/i', $a3[1])) {
									$attr[strtoupper($a3[1])] = trim(strtoupper($a3[2]));
								} else {
									$attr[strtoupper($a3[1])] = trim($a3[2]);
								}
							}
						}
					}
					$this->OpenTag($tag, $attr, $a, $i); // mPDF 6
					/* -- CSS-POSITION -- */
					if ($this->inFixedPosBlock) {
						$this->fixedPosBlockBBox = array($tag, $attr, $this->x, $this->y);
						$this->fixedPosBlock = '';
						$this->fixedPosBlockDepth = 1;
					}
					/* -- END CSS-POSITION -- */
					if (preg_match('/\/$/', $e)) {
						$this->closeTag($tag, $a, $i);
					}
				}
			} // end TAG
		} //end of	foreach($a as $i=>$e)

		if ($close) {

			// Close any open block tags
			for ($b = $this->blklvl; $b > 0; $b--) {
				$this->CloseTag($this->blk[$b]['tag'], $a, $i);
			}

			// Output any text left in buffer
			if (count($this->textbuffer) && !$parseonly) {
				$this->printbuffer($this->textbuffer);
			}
			if (!$parseonly)
				$this->textbuffer = array();

			/* -- CSS-FLOAT -- */
			// If ended with a float, need to move to end page
			$currpos = $this->page * 1000 + $this->y;
			if (isset($this->blk[$this->blklvl]['float_endpos']) && $this->blk[$this->blklvl]['float_endpos'] > $currpos) {
				$old_page = $this->page;
				$new_page = intval($this->blk[$this->blklvl]['float_endpos'] / 1000);
				if ($old_page != $new_page) {
					$s = $this->PrintPageBackgrounds();
					// Writes after the marker so not overwritten later by page background etc.
					$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', '\\1' . "\n" . $s . "\n", $this->pages[$this->page]);
					$this->pageBackgrounds = array();
					$this->page = $new_page;
					$this->ResetMargins();
					$this->Reset();
					$this->pageoutput[$this->page] = array();
				}
				$this->y = (($this->blk[$this->blklvl]['float_endpos'] * 1000) % 1000000) / 1000; // mod changes operands to integers before processing
			}
			/* -- END CSS-FLOAT -- */

			/* -- CSS-IMAGE-FLOAT -- */
			$this->printfloatbuffer();
			/* -- END CSS-IMAGE-FLOAT -- */

			//Create Internal Links, if needed
			if (!empty($this->internallink)) {
				foreach ($this->internallink as $k => $v) {
					if (strpos($k, "#") !== false) {
						continue;
					} //ignore
					$ypos = $v['Y'];
					$pagenum = $v['PAGE'];
					$sharp = "#";
					while (array_key_exists($sharp . $k, $this->internallink)) {
						$internallink = $this->internallink[$sharp . $k];
						$this->SetLink($internallink, $ypos, $pagenum);
						$sharp .= "#";
					}
				}
			}

			$this->bufferoutput = false;

			/* -- CSS-POSITION -- */
			if (count($this->fixedPosBlockSave) && $sub != 4) {
				foreach ($this->fixedPosBlockSave AS $fpbs) {
					$old_page = $this->page;
					$this->page = $fpbs[2];
					$this->WriteFixedPosHTML($fpbs[0], 0, 0, 100, 100, 'auto', $fpbs[1]);  // 0,0,10,10 are overwritten by bbox
					$this->page = $old_page;
				}
				$this->fixedPosBlockSave = array();
			}
			/* -- END CSS-POSITION -- */
		}
	}

	

	function initialiseBlock(&$blk)
	{
		$blk['margin_top'] = 0;
		$blk['margin_left'] = 0;
		$blk['margin_bottom'] = 0;
		$blk['margin_right'] = 0;
		$blk['padding_top'] = 0;
		$blk['padding_left'] = 0;
		$blk['padding_bottom'] = 0;
		$blk['padding_right'] = 0;
		$blk['border_top']['w'] = 0;
		$blk['border_left']['w'] = 0;
		$blk['border_bottom']['w'] = 0;
		$blk['border_right']['w'] = 0;
		$blk['direction'] = 'ltr';
		$blk['hide'] = false;
		$blk['outer_left_margin'] = 0;
		$blk['outer_right_margin'] = 0;
		$blk['cascadeCSS'] = array();
		$blk['block-align'] = false;
		$blk['bgcolor'] = false;
		$blk['page_break_after_avoid'] = false;
		$blk['keep_block_together'] = false;
		$blk['float'] = false;
		$blk['line_height'] = '';
		$blk['margin_collapse'] = false;
	}

	function border_details($bd)
	{
		$prop = preg_split('/\s+/', trim($bd));

		if (isset($this->blk[$this->blklvl]['inner_width'])) {
			$refw = $this->blk[$this->blklvl]['inner_width'];
		} else if (isset($this->blk[$this->blklvl - 1]['inner_width'])) {
			$refw = $this->blk[$this->blklvl - 1]['inner_width'];
		} else {
			$refw = $this->w;
		}
		if (count($prop) == 1) {
			$bsize = $this->ConvertSize($prop[0], $refw, $this->FontSize, false);
			if ($bsize > 0) {
				return array('s' => 1, 'w' => $bsize, 'c' => $this->ConvertColor(0), 'style' => 'solid');
			} else {
				return array('w' => 0, 's' => 0);
			}
		} else if (count($prop) == 2) {
			// 1px solid
			if (in_array($prop[1], $this->borderstyles) || $prop[1] == 'none' || $prop[1] == 'hidden') {
				$prop[2] = '';
			}
			// solid #000000
			else if (in_array($prop[0], $this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden') {
				$prop[0] = '';
				$prop[1] = $prop[0];
				$prop[2] = $prop[1];
			}
			// 1px #000000
			else {
				$prop[1] = '';
				$prop[2] = $prop[1];
			}
		} else if (count($prop) == 3) {
			// Change #000000 1px solid to 1px solid #000000 (proper)
			if (substr($prop[0], 0, 1) == '#') {
				$tmp = $prop[0];
				$prop[0] = $prop[1];
				$prop[1] = $prop[2];
				$prop[2] = $tmp;
			}
			// Change solid #000000 1px to 1px solid #000000 (proper)
			else if (substr($prop[0], 1, 1) == '#') {
				$tmp = $prop[1];
				$prop[0] = $prop[2];
				$prop[1] = $prop[0];
				$prop[2] = $tmp;
			}
			// Change solid 1px #000000 to 1px solid #000000 (proper)
			else if (in_array($prop[0], $this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden') {
				$tmp = $prop[0];
				$prop[0] = $prop[1];
				$prop[1] = $tmp;
			}
		} else {
			return array();
		}
		// Size
		$bsize = $this->ConvertSize($prop[0], $refw, $this->FontSize, false);
		//color
		$coul = $this->ConvertColor($prop[2]); // returns array
		// Style
		$prop[1] = strtolower($prop[1]);
		if (in_array($prop[1], $this->borderstyles) && $bsize > 0) {
			$on = 1;
		} else if ($prop[1] == 'hidden') {
			$on = 1;
			$bsize = 0;
			$coul = '';
		} else if ($prop[1] == 'none') {
			$on = 0;
			$bsize = 0;
			$coul = '';
		} else {
			$on = 0;
			$bsize = 0;
			$coul = '';
			$prop[1] = '';
		}
		return array('s' => $on, 'w' => $bsize, 'c' => $coul, 'style' => $prop[1]);
	}

	/* -- END HTML-CSS -- */


	



	/* -- HTML-CSS -- */


	



	/* -- CSS-FLOAT -- */

// Added mPDF 3.0 Float DIV - CLEAR
	function ClearFloats($clear, $blklvl = 0)
	{
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($blklvl, true);
		$end = $currpos = ($this->page * 1000 + $this->y);
		if ($clear == 'BOTH' && ($l_exists || $r_exists)) {
			$this->pageoutput[$this->page] = array();
			$end = max($l_max, $r_max, $currpos);
		} else if ($clear == 'RIGHT' && $r_exists) {
			$this->pageoutput[$this->page] = array();
			$end = max($r_max, $currpos);
		} else if ($clear == 'LEFT' && $l_exists) {
			$this->pageoutput[$this->page] = array();
			$end = max($l_max, $currpos);
		} else {
			return;
		}
		$old_page = $this->page;
		$new_page = intval($end / 1000);
		if ($old_page != $new_page) {
			$s = $this->PrintPageBackgrounds();
			// Writes after the marker so not overwritten later by page background etc.
			$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', '\\1' . "\n" . $s . "\n", $this->pages[$this->page]);
			$this->pageBackgrounds = array();
			$this->page = $new_page;
		}
		$this->ResetMargins();
		$this->pageoutput[$this->page] = array();
		$this->y = (($end * 1000) % 1000000) / 1000; // mod changes operands to integers before processing
	}

// Added mPDF 3.0 Float DIV
	function GetFloatDivInfo($blklvl = 0, $clear = false)
	{
		// If blklvl specified, only returns floats at that level - for ClearFloats
		$l_exists = false;
		$r_exists = false;
		$l_max = 0;
		$r_max = 0;
		$l_width = 0;
		$r_width = 0;
		if (count($this->floatDivs)) {
			$currpos = ($this->page * 1000 + $this->y);
			foreach ($this->floatDivs AS $f) {
				if (($clear && $f['blockContext'] == $this->blk[$blklvl]['blockContext']) || (!$clear && $currpos >= $f['startpos'] && $currpos < ($f['endpos'] - 0.001) && $f['blklvl'] > $blklvl && $f['blockContext'] == $this->blk[$blklvl]['blockContext'])) {
					if ($f['side'] == 'L') {
						$l_exists = true;
						$l_max = max($l_max, $f['endpos']);
						$l_width = max($l_width, $f['w']);
					}
					if ($f['side'] == 'R') {
						$r_exists = true;
						$r_max = max($r_max, $f['endpos']);
						$r_width = max($r_width, $f['w']);
					}
				}
			}
		}
		return array($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width);
	}

	/* -- END CSS-FLOAT -- */

	function OpenTag($tag, $attr, &$ahtml, &$ihtml)
	{ // mPDF 6
		//Opening tag
		// mPDF 6
		// Correct for tags where HTML5 specifies optional end tags excluding table elements (cf WriteHTML() )
		if ($this->allow_html_optional_endtags) {
			if (isset($this->blk[$this->blklvl]['tag'])) {
				$closed = false;
				// li end tag may be omitted if immediately followed by another li element
				if (!$closed && $this->blk[$this->blklvl]['tag'] == 'LI' && $tag == 'LI') {
					$this->CloseTag('LI', $ahtml, $ihtml);
					$closed = true;
				}
				// dt end tag may be omitted if immediately followed by another dt element or a dd element
				if (!$closed && $this->blk[$this->blklvl]['tag'] == 'DT' && ($tag == 'DT' || $tag == 'DD')) {
					$this->CloseTag('DT', $ahtml, $ihtml);
					$closed = true;
				}
				// dd end tag may be omitted if immediately followed by another dd element or a dt element
				if (!$closed && $this->blk[$this->blklvl]['tag'] == 'DD' && ($tag == 'DT' || $tag == 'DD')) {
					$this->CloseTag('DD', $ahtml, $ihtml);
					$closed = true;
				}
				// p end tag may be omitted if immediately followed by an address, article, aside, blockquote, div, dl, fieldset, form,
				// h1, h2, h3, h4, h5, h6, hgroup, hr, main, nav, ol, p, pre, section, table, ul
				if (!$closed && $this->blk[$this->blklvl]['tag'] == 'P' && ($tag == 'P' || $tag == 'DIV' || $tag == 'H1' || $tag == 'H2' || $tag == 'H3' || $tag == 'H4' || $tag == 'H5' || $tag == 'H6' || $tag == 'UL' || $tag == 'OL' || $tag == 'TABLE' || $tag == 'PRE' || $tag == 'FORM' || $tag == 'ADDRESS' || $tag == 'BLOCKQUOTE' || $tag == 'CENTER' || $tag == 'DL' || $tag == 'HR' || $tag == 'ARTICLE' || $tag == 'ASIDE' || $tag == 'FIELDSET' || $tag == 'HGROUP' || $tag == 'MAIN' || $tag == 'NAV' || $tag == 'SECTION')) {
					$this->CloseTag('P', $ahtml, $ihtml);
					$closed = true;
				}
				// option end tag may be omitted if immediately followed by another option element (or if it is immediately followed by an optgroup element)
				if (!$closed && $this->blk[$this->blklvl]['tag'] == 'OPTION' && $tag == 'OPTION') {
					$this->CloseTag('OPTION', $ahtml, $ihtml);
					$closed = true;
				}
				// Table elements - see also WriteHTML()
				if (!$closed && ($tag == 'TD' || $tag == 'TH') && $this->lastoptionaltag == 'TD') {
					$this->CloseTag($this->lastoptionaltag, $ahtml, $ihtml);
					$closed = true;
				} // *TABLES*
				if (!$closed && ($tag == 'TD' || $tag == 'TH') && $this->lastoptionaltag == 'TH') {
					$this->CloseTag($this->lastoptionaltag, $ahtml, $ihtml);
					$closed = true;
				} // *TABLES*
				if (!$closed && $tag == 'TR' && $this->lastoptionaltag == 'TR') {
					$this->CloseTag($this->lastoptionaltag, $ahtml, $ihtml);
					$closed = true;
				} // *TABLES*
				if (!$closed && $tag == 'TR' && $this->lastoptionaltag == 'TD') {
					$this->CloseTag($this->lastoptionaltag, $ahtml, $ihtml);
					$this->CloseTag('TR', $ahtml, $ihtml);
					$this->CloseTag('THEAD', $ahtml, $ihtml);
					$closed = true;
				} // *TABLES*
				if (!$closed && $tag == 'TR' && $this->lastoptionaltag == 'TH') {
					$this->CloseTag($this->lastoptionaltag, $ahtml, $ihtml);
					$this->CloseTag('TR', $ahtml, $ihtml);
					$this->CloseTag('THEAD', $ahtml, $ihtml);
					$closed = true;
				} // *TABLES*
			}
		}

		$align = array('left' => 'L', 'center' => 'C', 'right' => 'R', 'top' => 'T', 'text-top' => 'TT', 'middle' => 'M', 'baseline' => 'BS', 'bottom' => 'B', 'text-bottom' => 'TB', 'justify' => 'J');



		switch ($tag) {

			case 'DOTTAB':
				$objattr = array();
				$objattr['type'] = 'dottab';
				$dots = str_repeat('.', 3) . "  "; // minimum number of dots
				$objattr['width'] = $this->GetStringWidth($dots);
				$objattr['margin_top'] = 0;
				$objattr['margin_bottom'] = 0;
				$objattr['margin_left'] = 0;
				$objattr['margin_right'] = 0;
				$objattr['height'] = 0;
				$objattr['colorarray'] = $this->colorarray;
				$objattr['border_top']['w'] = 0;
				$objattr['border_bottom']['w'] = 0;
				$objattr['border_left']['w'] = 0;
				$objattr['border_right']['w'] = 0;
				$objattr['vertical_align'] = 'BS'; // mPDF 6 DOTTAB

				$properties = $this->cssmgr->MergeCSS('INLINE', $tag, $attr);
				if (isset($properties['OUTDENT'])) {
					$objattr['outdent'] = $this->ConvertSize($properties['OUTDENT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				} else if (isset($attr['OUTDENT'])) {
					$objattr['outdent'] = $this->ConvertSize($attr['OUTDENT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				} else {
					$objattr['outdent'] = 0;
				}

				$objattr['fontfamily'] = $this->FontFamily;
				$objattr['fontsize'] = $this->FontSizePt;

				$e = "\xbb\xa4\xactype=dottab,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
				/* -- TABLES -- */
				// Output it to buffers
				if ($this->tableLevel) {
					if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
						$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
					} elseif ($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
						$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
					}
					$this->cell[$this->row][$this->col]['s'] = 0; // reset
					$this->_saveCellTextBuffer($e);
				} else {
					/* -- END TABLES -- */
					$this->_saveTextBuffer($e);
				} // *TABLES*
				break;

			case 'PAGEHEADER':
			case 'PAGEFOOTER':
				$this->ignorefollowingspaces = true;
				if ($attr['NAME']) {
					$pname = $attr['NAME'];
				} else {
					$pname = '_nonhtmldefault';
				} // mPDF 6

				$p = array(); // mPDF 6
				$p['L'] = array();
				$p['C'] = array();
				$p['R'] = array();
				$p['L']['font-style'] = '';
				$p['C']['font-style'] = '';
				$p['R']['font-style'] = '';

				if (isset($attr['CONTENT-LEFT'])) {
					$p['L']['content'] = $attr['CONTENT-LEFT'];
				}
				if (isset($attr['CONTENT-CENTER'])) {
					$p['C']['content'] = $attr['CONTENT-CENTER'];
				}
				if (isset($attr['CONTENT-RIGHT'])) {
					$p['R']['content'] = $attr['CONTENT-RIGHT'];
				}

				if (isset($attr['HEADER-STYLE']) || isset($attr['FOOTER-STYLE'])) { // font-family,size,weight,style,color
					if ($tag == 'PAGEHEADER') {
						$properties = $this->cssmgr->readInlineCSS($attr['HEADER-STYLE']);
					} else {
						$properties = $this->cssmgr->readInlineCSS($attr['FOOTER-STYLE']);
					}
					if (isset($properties['FONT-FAMILY'])) {
						$p['L']['font-family'] = $properties['FONT-FAMILY'];
						$p['C']['font-family'] = $properties['FONT-FAMILY'];
						$p['R']['font-family'] = $properties['FONT-FAMILY'];
					}
					if (isset($properties['FONT-SIZE'])) {
						$p['L']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * _MPDFK;
						$p['C']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * _MPDFK;
						$p['R']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * _MPDFK;
					}
					if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT'] == 'bold') {
						$p['L']['font-style'] = 'B';
						$p['C']['font-style'] = 'B';
						$p['R']['font-style'] = 'B';
					}
					if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE'] == 'italic') {
						$p['L']['font-style'] .= 'I';
						$p['C']['font-style'] .= 'I';
						$p['R']['font-style'] .= 'I';
					}
					if (isset($properties['COLOR'])) {
						$p['L']['color'] = $properties['COLOR'];
						$p['C']['color'] = $properties['COLOR'];
						$p['R']['color'] = $properties['COLOR'];
					}
				}
				if (isset($attr['HEADER-STYLE-LEFT']) || isset($attr['FOOTER-STYLE-LEFT'])) {
					if ($tag == 'PAGEHEADER') {
						$properties = $this->cssmgr->readInlineCSS($attr['HEADER-STYLE-LEFT']);
					} else {
						$properties = $this->cssmgr->readInlineCSS($attr['FOOTER-STYLE-LEFT']);
					}
					if (isset($properties['FONT-FAMILY'])) {
						$p['L']['font-family'] = $properties['FONT-FAMILY'];
					}
					if (isset($properties['FONT-SIZE'])) {
						$p['L']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * _MPDFK;
					}
					if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT'] == 'bold') {
						$p['L']['font-style'] = 'B';
					}
					if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE'] == 'italic') {
						$p['L']['font-style'] .='I';
					}
					if (isset($properties['COLOR'])) {
						$p['L']['color'] = $properties['COLOR'];
					}
				}
				if (isset($attr['HEADER-STYLE-CENTER']) || isset($attr['FOOTER-STYLE-CENTER'])) {
					if ($tag == 'PAGEHEADER') {
						$properties = $this->cssmgr->readInlineCSS($attr['HEADER-STYLE-CENTER']);
					} else {
						$properties = $this->cssmgr->readInlineCSS($attr['FOOTER-STYLE-CENTER']);
					}
					if (isset($properties['FONT-FAMILY'])) {
						$p['C']['font-family'] = $properties['FONT-FAMILY'];
					}
					if (isset($properties['FONT-SIZE'])) {
						$p['C']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * _MPDFK;
					}
					if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT'] == 'bold') {
						$p['C']['font-style'] = 'B';
					}
					if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE'] == 'italic') {
						$p['C']['font-style'] .= 'I';
					}
					if (isset($properties['COLOR'])) {
						$p['C']['color'] = $properties['COLOR'];
					}
				}
				if (isset($attr['HEADER-STYLE-RIGHT']) || isset($attr['FOOTER-STYLE-RIGHT'])) {
					if ($tag == 'PAGEHEADER') {
						$properties = $this->cssmgr->readInlineCSS($attr['HEADER-STYLE-RIGHT']);
					} else {
						$properties = $this->cssmgr->readInlineCSS($attr['FOOTER-STYLE-RIGHT']);
					}
					if (isset($properties['FONT-FAMILY'])) {
						$p['R']['font-family'] = $properties['FONT-FAMILY'];
					}
					if (isset($properties['FONT-SIZE'])) {
						$p['R']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * _MPDFK;
					}
					if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT'] == 'bold') {
						$p['R']['font-style'] = 'B';
					}
					if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE'] == 'italic') {
						$p['R']['font-style'] .= 'I';
					}
					if (isset($properties['COLOR'])) {
						$p['R']['color'] = $properties['COLOR'];
					}
				}
				if (isset($attr['LINE']) && $attr['LINE']) { // 0|1|on|off
					if ($attr['LINE'] == '1' || strtoupper($attr['LINE']) == 'ON') {
						$lineset = 1;
					} else {
						$lineset = 0;
					}
					$p['line'] = $lineset;
				}
				// mPDF 6
				if ($tag == 'PAGEHEADER') {
					$this->DefHeaderByName($pname, $p);
				} else {
					$this->DefFooterByName($pname, $p);
				}
				break;


			case 'SETPAGEHEADER': // mPDF 6
			case 'SETPAGEFOOTER':
			case 'SETHTMLPAGEHEADER':
			case 'SETHTMLPAGEFOOTER':
				$this->ignorefollowingspaces = true;
				if (isset($attr['NAME']) && $attr['NAME']) {
					$pname = $attr['NAME'];
				} else if ($tag == 'SETPAGEHEADER' || $tag == 'SETPAGEFOOTER') {
					$pname = '_nonhtmldefault';
				} // mPDF 6
				else {
					$pname = '_default';
				}
				if (isset($attr['PAGE']) && $attr['PAGE']) {  // O|odd|even|E|ALL|[blank]
					if (strtoupper($attr['PAGE']) == 'O' || strtoupper($attr['PAGE']) == 'ODD') {
						$side = 'odd';
					} else if (strtoupper($attr['PAGE']) == 'E' || strtoupper($attr['PAGE']) == 'EVEN') {
						$side = 'even';
					} else if (strtoupper($attr['PAGE']) == 'ALL') {
						$side = 'both';
					} else {
						$side = 'odd';
					}
				} else {
					$side = 'odd';
				}
				if (isset($attr['VALUE']) && $attr['VALUE']) {  // -1|1|on|off
					if ($attr['VALUE'] == '1' || strtoupper($attr['VALUE']) == 'ON') {
						$set = 1;
					} else if ($attr['VALUE'] == '-1' || strtoupper($attr['VALUE']) == 'OFF') {
						$set = 0;
					} else {
						$set = 1;
					}
				} else {
					$set = 1;
				}
				if (isset($attr['SHOW-THIS-PAGE']) && $attr['SHOW-THIS-PAGE'] && ($tag == 'SETHTMLPAGEHEADER' || $tag == 'SETPAGEHEADER')) {
					$write = 1;
				} else {
					$write = 0;
				}
				if ($side == 'odd' || $side == 'both') {
					if ($set && ($tag == 'SETHTMLPAGEHEADER' || $tag == 'SETPAGEHEADER')) {
						$this->SetHTMLHeader($this->pageHTMLheaders[$pname], 'O', $write);
					} else if ($set && ($tag == 'SETHTMLPAGEFOOTER' || $tag == 'SETPAGEFOOTER')) {
						$this->SetHTMLFooter($this->pageHTMLfooters[$pname], 'O');
					} else if ($tag == 'SETHTMLPAGEHEADER' || $tag == 'SETPAGEHEADER') {
						$this->SetHTMLHeader('', 'O');
					} else {
						$this->SetHTMLFooter('', 'O');
					}
				}
				if ($side == 'even' || $side == 'both') {
					if ($set && ($tag == 'SETHTMLPAGEHEADER' || $tag == 'SETPAGEHEADER')) {
						$this->SetHTMLHeader($this->pageHTMLheaders[$pname], 'E', $write);
					} else if ($set && ($tag == 'SETHTMLPAGEFOOTER' || $tag == 'SETPAGEFOOTER')) {
						$this->SetHTMLFooter($this->pageHTMLfooters[$pname], 'E');
					} else if ($tag == 'SETHTMLPAGEHEADER' || $tag == 'SETPAGEHEADER') {
						$this->SetHTMLHeader('', 'E');
					} else {
						$this->SetHTMLFooter('', 'E');
					}
				}
				break;



			/* -- TOC -- */
			case 'TOC': //added custom-tag - set Marker for insertion later of ToC
				if (!class_exists('tocontents', false)) {
					include(_MPDF_PATH . 'classes/tocontents.php');
				}
				if (empty($this->tocontents)) {
					$this->tocontents = new tocontents($this);
				}
				$this->tocontents->openTagTOC($attr);
				break;



			case 'TOCPAGEBREAK': // custom-tag - set Marker for insertion later of ToC AND adds PAGEBREAK
				if (!class_exists('tocontents', false)) {
					include(_MPDF_PATH . 'classes/tocontents.php');
				}
				if (empty($this->tocontents)) {
					$this->tocontents = new tocontents($this);
				}
				list($isbreak, $toc_id) = $this->tocontents->openTagTOCPAGEBREAK($attr);
				if ($isbreak)
					break;
				if (!isset($attr['RESETPAGENUM']) || $attr['RESETPAGENUM'] < 1) {
					$attr['RESETPAGENUM'] = 1;
				} // mPDF 6
			// No break - continues as PAGEBREAK...
			/* -- END TOC -- */


			case 'PAGE_BREAK': //custom-tag
			case 'PAGEBREAK': //custom-tag
			case 'NEWPAGE': //custom-tag
			case 'FORMFEED': //custom-tag

				if (isset($attr['SHEET-SIZE'])) {
					// Convert to same types as accepted in initial mPDF() A4, A4-L, or array(w,h)
					$prop = preg_split('/\s+/', trim($attr['SHEET-SIZE']));
					if (count($prop) == 2) {
						$newformat = array($this->ConvertSize($prop[0]), $this->ConvertSize($prop[1]));
					} else {
						$newformat = $attr['SHEET-SIZE'];
					}
				} else {
					$newformat = '';
				}

				$save_blklvl = $this->blklvl;
				$save_blk = $this->blk;
				$save_silp = $this->saveInlineProperties();
				$save_ilp = $this->InlineProperties;
				$save_bflp = $this->InlineBDF;
				$save_bflpc = $this->InlineBDFctr; // mPDF 6

				$mgr = $mgl = $mgt = $mgb = $mgh = $mgf = '';
				if (isset($attr['MARGIN-RIGHT'])) {
					$mgr = $this->ConvertSize($attr['MARGIN-RIGHT'], $this->w, $this->FontSize, false);
				}
				if (isset($attr['MARGIN-LEFT'])) {
					$mgl = $this->ConvertSize($attr['MARGIN-LEFT'], $this->w, $this->FontSize, false);
				}
				if (isset($attr['MARGIN-TOP'])) {
					$mgt = $this->ConvertSize($attr['MARGIN-TOP'], $this->w, $this->FontSize, false);
				}
				if (isset($attr['MARGIN-BOTTOM'])) {
					$mgb = $this->ConvertSize($attr['MARGIN-BOTTOM'], $this->w, $this->FontSize, false);
				}
				if (isset($attr['MARGIN-HEADER'])) {
					$mgh = $this->ConvertSize($attr['MARGIN-HEADER'], $this->w, $this->FontSize, false);
				}
				if (isset($attr['MARGIN-FOOTER'])) {
					$mgf = $this->ConvertSize($attr['MARGIN-FOOTER'], $this->w, $this->FontSize, false);
				}
				$ohname = $ehname = $ofname = $efname = '';
				if (isset($attr['ODD-HEADER-NAME'])) {
					$ohname = $attr['ODD-HEADER-NAME'];
				}
				if (isset($attr['EVEN-HEADER-NAME'])) {
					$ehname = $attr['EVEN-HEADER-NAME'];
				}
				if (isset($attr['ODD-FOOTER-NAME'])) {
					$ofname = $attr['ODD-FOOTER-NAME'];
				}
				if (isset($attr['EVEN-FOOTER-NAME'])) {
					$efname = $attr['EVEN-FOOTER-NAME'];
				}
				$ohvalue = $ehvalue = $ofvalue = $efvalue = 0;
				if (isset($attr['ODD-HEADER-VALUE']) && ($attr['ODD-HEADER-VALUE'] == '1' || strtoupper($attr['ODD-HEADER-VALUE']) == 'ON')) {
					$ohvalue = 1;
				} else if (isset($attr['ODD-HEADER-VALUE']) && ($attr['ODD-HEADER-VALUE'] == '-1' || strtoupper($attr['ODD-HEADER-VALUE']) == 'OFF')) {
					$ohvalue = -1;
				}
				if (isset($attr['EVEN-HEADER-VALUE']) && ($attr['EVEN-HEADER-VALUE'] == '1' || strtoupper($attr['EVEN-HEADER-VALUE']) == 'ON')) {
					$ehvalue = 1;
				} else if (isset($attr['EVEN-HEADER-VALUE']) && ($attr['EVEN-HEADER-VALUE'] == '-1' || strtoupper($attr['EVEN-HEADER-VALUE']) == 'OFF')) {
					$ehvalue = -1;
				}
				if (isset($attr['ODD-FOOTER-VALUE']) && ($attr['ODD-FOOTER-VALUE'] == '1' || strtoupper($attr['ODD-FOOTER-VALUE']) == 'ON')) {
					$ofvalue = 1;
				} else if (isset($attr['ODD-FOOTER-VALUE']) && ($attr['ODD-FOOTER-VALUE'] == '-1' || strtoupper($attr['ODD-FOOTER-VALUE']) == 'OFF')) {
					$ofvalue = -1;
				}
				if (isset($attr['EVEN-FOOTER-VALUE']) && ($attr['EVEN-FOOTER-VALUE'] == '1' || strtoupper($attr['EVEN-FOOTER-VALUE']) == 'ON')) {
					$efvalue = 1;
				} else if (isset($attr['EVEN-FOOTER-VALUE']) && ($attr['EVEN-FOOTER-VALUE'] == '-1' || strtoupper($attr['EVEN-FOOTER-VALUE']) == 'OFF')) {
					$efvalue = -1;
				}

				if (isset($attr['ORIENTATION']) && (strtoupper($attr['ORIENTATION']) == 'L' || strtoupper($attr['ORIENTATION']) == 'LANDSCAPE')) {
					$orient = 'L';
				} else if (isset($attr['ORIENTATION']) && (strtoupper($attr['ORIENTATION']) == 'P' || strtoupper($attr['ORIENTATION']) == 'PORTRAIT')) {
					$orient = 'P';
				} else {
					$orient = $this->CurOrientation;
				}

				if (isset($attr['PAGE-SELECTOR']) && $attr['PAGE-SELECTOR']) {
					$pagesel = $attr['PAGE-SELECTOR'];
				} else {
					$pagesel = '';
				}

				// mPDF 6 pagebreaktype
				$pagebreaktype = $this->defaultPagebreakType;
				if ($tag == 'FORMFEED') {
					$pagebreaktype = 'slice';
				} // can be overridden by PAGE-BREAK-TYPE
				$startpage = $this->page;
				if (isset($attr['PAGE-BREAK-TYPE'])) {
					if (strtolower($attr['PAGE-BREAK-TYPE']) == 'cloneall' || strtolower($attr['PAGE-BREAK-TYPE']) == 'clonebycss' || strtolower($attr['PAGE-BREAK-TYPE']) == 'slice') {
						$pagebreaktype = strtolower($attr['PAGE-BREAK-TYPE']);
					}
				}
				if ($tag == 'TOCPAGEBREAK') {
					$pagebreaktype = 'cloneall';
				} else if ($this->ColActive) {
					$pagebreaktype = 'cloneall';
				}
				// Any change in headers/footers (may need to _getHtmlHeight), or page size/orientation, @page selector, or margins - force cloneall
				else if ($mgr !== '' || $mgl !== '' || $mgt !== '' || $mgb !== '' || $mgh !== '' || $mgf !== '' ||
					$ohname !== '' || $ehname !== '' || $ofname !== '' || $efname !== '' ||
					$ohvalue || $ehvalue || $ofvalue || $efvalue ||
					$orient != $this->CurOrientation || $newformat || $pagesel) {
					$pagebreaktype = 'cloneall';
				}

				// mPDF 6 pagebreaktype
				$this->_preForcedPagebreak($pagebreaktype);

				$this->ignorefollowingspaces = true;


				$resetpagenum = '';
				$pagenumstyle = '';
				$suppress = '';
				if (isset($attr['RESETPAGENUM'])) {
					$resetpagenum = $attr['RESETPAGENUM'];
				}
				if (isset($attr['PAGENUMSTYLE'])) {
					$pagenumstyle = $attr['PAGENUMSTYLE'];
				}
				if (isset($attr['SUPPRESS'])) {
					$suppress = $attr['SUPPRESS'];
				}

				if ($tag == 'TOCPAGEBREAK') {
					$type = 'NEXT-ODD';
				} else if (isset($attr['TYPE'])) {
					$type = strtoupper($attr['TYPE']);
				} else {
					$type = '';
				}

				if ($type == 'E' || $type == 'EVEN') {
					$this->AddPage($orient, 'E', $resetpagenum, $pagenumstyle, $suppress, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
				} else if ($type == 'O' || $type == 'ODD') {
					$this->AddPage($orient, 'O', $resetpagenum, $pagenumstyle, $suppress, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
				} else if ($type == 'NEXT-ODD') {
					$this->AddPage($orient, 'NEXT-ODD', $resetpagenum, $pagenumstyle, $suppress, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
				} else if ($type == 'NEXT-EVEN') {
					$this->AddPage($orient, 'NEXT-EVEN', $resetpagenum, $pagenumstyle, $suppress, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
				} else {
					$this->AddPage($orient, '', $resetpagenum, $pagenumstyle, $suppress, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
				}

				/* -- TOC -- */
				if ($tag == 'TOCPAGEBREAK') {
					if ($toc_id) {
						$this->tocontents->m_TOC[$toc_id]['TOCmark'] = $this->page;
					} else {
						$this->tocontents->TOCmark = $this->page;
					}
				}
				/* -- END TOC -- */

				// mPDF 6 pagebreaktype
				$this->_postForcedPagebreak($pagebreaktype, $startpage, $save_blk, $save_blklvl);

				$this->InlineProperties = $save_ilp;
				$this->InlineBDF = $save_bflp;
				$this->InlineBDFctr = $save_bflpc; // mPDF 6
				$this->restoreInlineProperties($save_silp);

				break;


			/* -- TOC -- */
			case 'TOCENTRY':
				if (isset($attr['CONTENT']) && $attr['CONTENT']) {
					$objattr = array();
					$objattr['CONTENT'] = htmlspecialchars_decode($attr['CONTENT'], ENT_QUOTES);
					$objattr['type'] = 'toc';
					$objattr['vertical-align'] = 'T';
					if (isset($attr['LEVEL']) && $attr['LEVEL']) {
						$objattr['toclevel'] = $attr['LEVEL'];
					} else {
						$objattr['toclevel'] = 0;
					}
					if (isset($attr['NAME']) && $attr['NAME']) {
						$objattr['toc_id'] = $attr['NAME'];
					} else {
						$objattr['toc_id'] = 0;
					}
					$e = "\xbb\xa4\xactype=toc,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
					if ($this->tableLevel) {
						$this->cell[$this->row][$this->col]['textbuffer'][] = array($e);
					} // *TABLES*
					else { // *TABLES*
						$this->textbuffer[] = array($e);
					} // *TABLES*
				}
				break;
			/* -- END TOC -- */

			/* -- INDEX -- */
			case 'INDEXENTRY':
				if (isset($attr['CONTENT']) && $attr['CONTENT']) {
					if (isset($attr['XREF']) && $attr['XREF']) {
						$this->IndexEntry(htmlspecialchars_decode($attr['CONTENT'], ENT_QUOTES), $attr['XREF']);
						break;
					}
					$objattr = array();
					$objattr['CONTENT'] = htmlspecialchars_decode($attr['CONTENT'], ENT_QUOTES);
					$objattr['type'] = 'indexentry';
					$objattr['vertical-align'] = 'T';
					$e = "\xbb\xa4\xactype=indexentry,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
					if ($this->tableLevel) {
						$this->cell[$this->row][$this->col]['textbuffer'][] = array($e);
					}  // *TABLES*
					else { // *TABLES*
						$this->textbuffer[] = array($e);
					} // *TABLES*
				}
				break;


			case 'INDEXINSERT':
				if (isset($attr['COLLATION'])) {
					$indexCollationLocale = $attr['COLLATION'];
				} else {
					$indexCollationLocale = '';
				}
				if (isset($attr['COLLATION-GROUP'])) {
					$indexCollationGroup = $attr['COLLATION-GROUP'];
				} else {
					$indexCollationGroup = '';
				}
				if (isset($attr['USEDIVLETTERS']) && (strtoupper($attr['USEDIVLETTERS']) == 'OFF' || $attr['USEDIVLETTERS'] == -1 || $attr['USEDIVLETTERS'] === '0')) {
					$usedivletters = 0;
				} else {
					$usedivletters = 1;
				}
				if (isset($attr['LINKS']) && (strtoupper($attr['LINKS']) == 'ON' || $attr['LINKS'] == 1)) {
					$links = true;
				} else {
					$links = false;
				}
				$this->InsertIndex($usedivletters, $links, $indexCollationLocale, $indexCollationGroup);

				break;
			/* -- END INDEX -- */

			/* -- WATERMARK -- */

			case 'WATERMARKTEXT':
				if (isset($attr['CONTENT']) && $attr['CONTENT']) {
					$txt = htmlspecialchars_decode($attr['CONTENT'], ENT_QUOTES);
				} else {
					$txt = '';
				}
				if (isset($attr['ALPHA']) && $attr['ALPHA'] > 0) {
					$alpha = $attr['ALPHA'];
				} else {
					$alpha = -1;
				}
				$this->SetWatermarkText($txt, $alpha);
				break;


			case 'WATERMARKIMAGE':
				if (isset($attr['SRC'])) {
					$src = $attr['SRC'];
				} else {
					$src = '';
				}
				if (isset($attr['ALPHA']) && $attr['ALPHA'] > 0) {
					$alpha = $attr['ALPHA'];
				} else {
					$alpha = -1;
				}
				if (isset($attr['SIZE']) && $attr['SIZE']) {
					$size = $attr['SIZE'];
					if (strpos($size, ',')) {
						$size = explode(',', $size);
					}
				} else {
					$size = 'D';
				}
				if (isset($attr['POSITION']) && $attr['POSITION']) {  // mPDF 5.7.2
					$pos = $attr['POSITION'];
					if (strpos($pos, ',')) {
						$pos = explode(',', $pos);
					}
				} else {
					$pos = 'P';
				}
				$this->SetWatermarkImage($src, $alpha, $size, $pos);
				break;
			/* -- END WATERMARK -- */

			/* -- BOOKMARKS -- */
			case 'BOOKMARK':
				if (isset($attr['CONTENT'])) {
					$objattr = array();
					$objattr['CONTENT'] = htmlspecialchars_decode($attr['CONTENT'], ENT_QUOTES);
					$objattr['type'] = 'bookmark';
					if (isset($attr['LEVEL']) && $attr['LEVEL']) {
						$objattr['bklevel'] = $attr['LEVEL'];
					} else {
						$objattr['bklevel'] = 0;
					}
					$e = "\xbb\xa4\xactype=bookmark,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
					if ($this->tableLevel) {
						$this->cell[$this->row][$this->col]['textbuffer'][] = array($e);
					} // *TABLES*
					else { // *TABLES*
						$this->textbuffer[] = array($e);
					} // *TABLES*
				}
				break;
			/* -- END BOOKMARKS -- */

			/* -- ANNOTATIONS -- */
			case 'ANNOTATION':

				//if (isset($attr['CONTENT']) && !$this->writingHTMLheader && !$this->writingHTMLfooter) {	// Stops annotations in FixedPos
				if (isset($attr['CONTENT'])) {
					$objattr = array();
					$objattr['margin_top'] = 0;
					$objattr['margin_bottom'] = 0;
					$objattr['margin_left'] = 0;
					$objattr['margin_right'] = 0;
					$objattr['width'] = 0;
					$objattr['height'] = 0;
					$objattr['border_top']['w'] = 0;
					$objattr['border_bottom']['w'] = 0;
					$objattr['border_left']['w'] = 0;
					$objattr['border_right']['w'] = 0;
					$objattr['CONTENT'] = htmlspecialchars_decode($attr['CONTENT'], ENT_QUOTES);
					$objattr['type'] = 'annot';
					$objattr['POPUP'] = '';
				} else {
					break;
				}
				if (isset($attr['POS-X'])) {
					$objattr['POS-X'] = $attr['POS-X'];
				} else {
					$objattr['POS-X'] = 0;
				}
				if (isset($attr['POS-Y'])) {
					$objattr['POS-Y'] = $attr['POS-Y'];
				} else {
					$objattr['POS-Y'] = 0;
				}
				if (isset($attr['ICON'])) {
					$objattr['ICON'] = $attr['ICON'];
				} else {
					$objattr['ICON'] = 'Note';
				}
				if (isset($attr['AUTHOR'])) {
					$objattr['AUTHOR'] = $attr['AUTHOR'];
				} else if (isset($attr['TITLE'])) {
					$objattr['AUTHOR'] = $attr['TITLE'];
				} else {
					$objattr['AUTHOR'] = '';
				}
				if (isset($attr['FILE'])) {
					$objattr['FILE'] = $attr['FILE'];
				} else {
					$objattr['FILE'] = '';
				}
				if (isset($attr['SUBJECT'])) {
					$objattr['SUBJECT'] = $attr['SUBJECT'];
				} else {
					$objattr['SUBJECT'] = '';
				}
				if (isset($attr['OPACITY']) && $attr['OPACITY'] > 0 && $attr['OPACITY'] <= 1) {
					$objattr['OPACITY'] = $attr['OPACITY'];
				} else if ($this->annotMargin) {
					$objattr['OPACITY'] = 1;
				} else {
					$objattr['OPACITY'] = $this->annotOpacity;
				}
				if (isset($attr['COLOR'])) {
					$cor = $this->ConvertColor($attr['COLOR']);
					if ($cor) {
						$objattr['COLOR'] = $cor;
					} else {
						$objattr['COLOR'] = $this->ConvertColor('yellow');
					}
				} else {
					$objattr['COLOR'] = $this->ConvertColor('yellow');
				}

				if (isset($attr['POPUP']) && !empty($attr['POPUP'])) {
					$pop = preg_split('/\s+/', trim($attr['POPUP']));
					if (count($pop) > 1) {
						$objattr['POPUP'] = $pop;
					} else {
						$objattr['POPUP'] = true;
					}
				}
				$e = "\xbb\xa4\xactype=annot,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
				if ($this->tableLevel) {
					$this->cell[$this->row][$this->col]['textbuffer'][] = array($e);
				} // *TABLES*
				else { // *TABLES*
					$this->textbuffer[] = array($e);
				} // *TABLES*
				break;
			/* -- END ANNOTATIONS -- */


			/* -- COLUMNS -- */
			case 'COLUMNS': //added custom-tag
				if (isset($attr['COLUMN-COUNT']) && ($attr['COLUMN-COUNT'] || $attr['COLUMN-COUNT'] === '0')) {
					// Close any open block tags
					for ($b = $this->blklvl; $b > 0; $b--) {
						$this->CloseTag($this->blk[$b]['tag'], $ahtml, $ihtml);
					}
					if (!empty($this->textbuffer)) { //Output previously buffered content
						$this->printbuffer($this->textbuffer);
						$this->textbuffer = array();
					}

					if (isset($attr['VALIGN']) && $attr['VALIGN']) {
						if ($attr['VALIGN'] == 'J') {
							$valign = 'J';
						} else {
							$valign = $align[$attr['VALIGN']];
						}
					} else {
						$valign = '';
					}
					if (isset($attr['COLUMN-GAP']) && $attr['COLUMN-GAP']) {
						$this->SetColumns($attr['COLUMN-COUNT'], $valign, $attr['COLUMN-GAP']);
					} else {
						$this->SetColumns($attr['COLUMN-COUNT'], $valign);
					}
				}
				$this->ignorefollowingspaces = true;
				break;

			case 'COLUMN_BREAK': //custom-tag
			case 'COLUMNBREAK': //custom-tag
			case 'NEWCOLUMN': //custom-tag
				$this->ignorefollowingspaces = true;
				$this->NewColumn();
				$this->ColumnAdjust = false; // disables all column height adjustment for the page.
				break;

			/* -- END COLUMNS -- */


			case 'TTZ':
				$this->ttz = true;
				$this->InlineProperties[$tag] = $this->saveInlineProperties();
				$this->setCSS(array('FONT-FAMILY' => 'czapfdingbats', 'FONT-WEIGHT' => 'normal', 'FONT-STYLE' => 'normal'), 'INLINE');
				break;

			case 'TTS':
				$this->tts = true;
				$this->InlineProperties[$tag] = $this->saveInlineProperties();
				$this->setCSS(array('FONT-FAMILY' => 'csymbol', 'FONT-WEIGHT' => 'normal', 'FONT-STYLE' => 'normal'), 'INLINE');
				break;

			case 'TTA':
				$this->tta = true;
				$this->InlineProperties[$tag] = $this->saveInlineProperties();

				if (in_array($this->FontFamily, $this->mono_fonts)) {
					$this->setCSS(array('FONT-FAMILY' => 'ccourier'), 'INLINE');
				} else if (in_array($this->FontFamily, $this->serif_fonts)) {
					$this->setCSS(array('FONT-FAMILY' => 'ctimes'), 'INLINE');
				} else {
					$this->setCSS(array('FONT-FAMILY' => 'chelvetica'), 'INLINE');
				}
				break;



			// INLINE PHRASES OR STYLES
			case 'SUB':
			case 'SUP':
			case 'ACRONYM':
			case 'BIG':
			case 'SMALL':
			case 'INS':
			case 'S':
			case 'STRIKE':
			case 'DEL':
			case 'STRONG':
			case 'CITE':
			case 'Q':
			case 'EM':
			case 'B':
			case 'I':
			case 'U':
			case 'SAMP':
			case 'CODE':
			case 'KBD':
			case 'TT':
			case 'VAR':
			case 'FONT':
			case 'MARK':
			case 'TIME':
			case 'BDO': // mPDF 6
			case 'BDI': // mPDF 6

			case 'SPAN':
				/* -- ANNOTATIONS -- */
				if ($this->title2annots && isset($attr['TITLE'])) {
					$objattr = array();
					$objattr['margin_top'] = 0;
					$objattr['margin_bottom'] = 0;
					$objattr['margin_left'] = 0;
					$objattr['margin_right'] = 0;
					$objattr['width'] = 0;
					$objattr['height'] = 0;
					$objattr['border_top']['w'] = 0;
					$objattr['border_bottom']['w'] = 0;
					$objattr['border_left']['w'] = 0;
					$objattr['border_right']['w'] = 0;

					$objattr['CONTENT'] = $attr['TITLE'];
					$objattr['type'] = 'annot';
					$objattr['POS-X'] = 0;
					$objattr['POS-Y'] = 0;
					$objattr['ICON'] = 'Comment';
					$objattr['AUTHOR'] = '';
					$objattr['SUBJECT'] = '';
					$objattr['OPACITY'] = $this->annotOpacity;
					$objattr['COLOR'] = $this->ConvertColor('yellow');
					$annot = "\xbb\xa4\xactype=annot,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
				}
				/* -- END ANNOTATIONS -- */

				// mPDF 5.7.3 Inline tags
				if (!isset($this->InlineProperties[$tag])) {
					$this->InlineProperties[$tag] = array($this->saveInlineProperties());
				} else {
					$this->InlineProperties[$tag][] = $this->saveInlineProperties();
				}
				if (isset($annot)) {  // *ANNOTATIONS*
					if (!isset($this->InlineAnnots[$tag])) {
						$this->InlineAnnots[$tag] = array($annot);
					} // *ANNOTATIONS*
					else {
						$this->InlineAnnots[$tag][] = $annot;
					} // *ANNOTATIONS*
				} // *ANNOTATIONS*

				$properties = $this->cssmgr->MergeCSS('INLINE', $tag, $attr);
				if (!empty($properties))
					$this->setCSS($properties, 'INLINE');

				// mPDF 6 Bidirectional formatting for inline elements
				$bdf = false;
				$bdf2 = '';
				$popd = '';

				// Get current direction
				if (isset($this->blk[$this->blklvl]['direction'])) {
					$currdir = $this->blk[$this->blklvl]['direction'];
				} else {
					$currdir = 'ltr';
				}
				if ($this->tableLevel && isset($this->cell[$this->row][$this->col]['direction']) && $this->cell[$this->row][$this->col]['direction'] == 'rtl') {
					$currdir = 'rtl';
				}
				if (isset($attr['DIR']) and $attr['DIR'] != '') {
					$currdir = strtolower($attr['DIR']);
				}
				if (isset($properties['DIRECTION'])) {
					$currdir = strtolower($properties['DIRECTION']);
				}

				// mPDF 6 bidi
				// cf. http://www.w3.org/TR/css3-writing-modes/#unicode-bidi
				if ($tag == 'BDO') {
					if (isset($attr['DIR']) and strtolower($attr['DIR']) == 'rtl') {
						$bdf = 0x202E;
						$popd = 'RLOPDF';
					} // U+202E RLO
					else if (isset($attr['DIR']) and strtolower($attr['DIR']) == 'ltr') {
						$bdf = 0x202D;
						$popd = 'LROPDF';
					} // U+202D LRO
				} else if ($tag == 'BDI') {
					if (isset($attr['DIR']) and strtolower($attr['DIR']) == 'rtl') {
						$bdf = 0x2067;
						$popd = 'RLIPDI';
					} // U+2067 RLI
					else if (isset($attr['DIR']) and strtolower($attr['DIR']) == 'ltr') {
						$bdf = 0x2066;
						$popd = 'LRIPDI';
					} // U+2066 LRI
					else {
						$bdf = 0x2068;
						$popd = 'FSIPDI';
					} // U+2068 FSI
				} else if (isset($properties ['UNICODE-BIDI']) && strtolower($properties ['UNICODE-BIDI']) == 'bidi-override') {
					if ($currdir == 'rtl') {
						$bdf = 0x202E;
						$popd = 'RLOPDF';
					} // U+202E RLO
					else {
						$bdf = 0x202D;
						$popd = 'LROPDF';
					} // U+202D LRO
				} else if (isset($properties ['UNICODE-BIDI']) && strtolower($properties ['UNICODE-BIDI']) == 'embed') {
					if ($currdir == 'rtl') {
						$bdf = 0x202B;
						$popd = 'RLEPDF';
					} // U+202B RLE
					else {
						$bdf = 0x202A;
						$popd = 'LREPDF';
					} // U+202A LRE
				} else if (isset($properties ['UNICODE-BIDI']) && strtolower($properties ['UNICODE-BIDI']) == 'isolate') {
					if ($currdir == 'rtl') {
						$bdf = 0x2067;
						$popd = 'RLIPDI';
					} // U+2067 RLI
					else {
						$bdf = 0x2066;
						$popd = 'LRIPDI';
					} // U+2066 LRI
				} else if (isset($properties ['UNICODE-BIDI']) && strtolower($properties ['UNICODE-BIDI']) == 'isolate-override') {
					if ($currdir == 'rtl') {
						$bdf = 0x2067;
						$bdf2 = 0x202E;
						$popd = 'RLIRLOPDFPDI';
					} // U+2067 RLI // U+202E RLO
					else {
						$bdf = 0x2066;
						$bdf2 = 0x202D;
						$popd = 'LRILROPDFPDI';
					} // U+2066 LRI  // U+202D LRO
				} else if (isset($properties ['UNICODE-BIDI']) && strtolower($properties ['UNICODE-BIDI']) == 'plaintext') {
					$bdf = 0x2068;
					$popd = 'FSIPDI'; // U+2068 FSI
				} else {
					if (isset($attr['DIR']) and strtolower($attr['DIR']) == 'rtl') {
						$bdf = 0x202B;
						$popd = 'RLEPDF';
					} // U+202B RLE
					else if (isset($attr['DIR']) and strtolower($attr['DIR']) == 'ltr') {
						$bdf = 0x202A;
						$popd = 'LREPDF';
					} // U+202A LRE
				}

				if ($bdf) {
					// mPDF 5.7.3 Inline tags
					if (!isset($this->InlineBDF[$tag])) {
						$this->InlineBDF[$tag] = array(array($popd, $this->InlineBDFctr));
					} else {
						$this->InlineBDF[$tag][] = array($popd, $this->InlineBDFctr);
					}
					$this->InlineBDFctr++;
					if ($bdf2) {
						$bdf2 = code2utf($bdf);
					}
					$this->OTLdata = array();
					if ($this->tableLevel) {
						$this->_saveCellTextBuffer(code2utf($bdf) . $bdf2);
					} else {
						$this->_saveTextBuffer(code2utf($bdf) . $bdf2);
					}
					$this->biDirectional = true;
				}

				break;


			case 'A':
				if (isset($attr['NAME']) and $attr['NAME'] != '') {
					$e = '';
					/* -- BOOKMARKS -- */
					if ($this->anchor2Bookmark) {
						$objattr = array();
						$objattr['CONTENT'] = htmlspecialchars_decode($attr['NAME'], ENT_QUOTES);
						$objattr['type'] = 'bookmark';
						if (isset($attr['LEVEL']) && $attr['LEVEL']) {
							$objattr['bklevel'] = $attr['LEVEL'];
						} else {
							$objattr['bklevel'] = 0;
						}
						$e = "\xbb\xa4\xactype=bookmark,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
					}
					/* -- END BOOKMARKS -- */
					if ($this->tableLevel) { // *TABLES*
						$this->_saveCellTextBuffer($e, '', $attr['NAME']); // *TABLES*
					} // *TABLES*
					else { // *TABLES*
						$this->_saveTextBuffer($e, '', $attr['NAME']); //an internal link (adds a space for recognition)
					} // *TABLES*
				}
				if (isset($attr['HREF'])) {
					$this->InlineProperties['A'] = $this->saveInlineProperties();
					$properties = $this->cssmgr->MergeCSS('INLINE', $tag, $attr);
					if (!empty($properties))
						$this->setCSS($properties, 'INLINE');
					$this->HREF = $attr['HREF']; // mPDF 5.7.4 URLs
				}
				break;

			case 'LEGEND':
				$this->InlineProperties['LEGEND'] = $this->saveInlineProperties();
				$properties = $this->cssmgr->MergeCSS('INLINE', $tag, $attr);
				if (!empty($properties))
					$this->setCSS($properties, 'INLINE');
				break;



			case 'PROGRESS':
			case 'METER':
				$this->inMeter = true;

				if (isset($attr['MAX']) && $attr['MAX']) {
					$max = $attr['MAX'];
				} else {
					$max = 1;
				}
				if (isset($attr['MIN']) && $attr['MIN'] && $tag == 'METER') {
					$min = $attr['MIN'];
				} else {
					$min = 0;
				}
				if ($max < $min) {
					$max = $min;
				}

				if (isset($attr['VALUE']) && ($attr['VALUE'] || $attr['VALUE'] === '0')) {
					$value = $attr['VALUE'];
					if ($value < $min) {
						$value = $min;
					} else if ($value > $max) {
						$value = $max;
					}
				} else {
					$value = '';
				}

				if (isset($attr['LOW']) && $attr['LOW']) {
					$low = $attr['LOW'];
				} else {
					$low = $min;
				}
				if ($low < $min) {
					$low = $min;
				} else if ($low > $max) {
					$low = $max;
				}
				if (isset($attr['HIGH']) && $attr['HIGH']) {
					$high = $attr['HIGH'];
				} else {
					$high = $max;
				}
				if ($high < $low) {
					$high = $low;
				} else if ($high > $max) {
					$high = $max;
				}
				if (isset($attr['OPTIMUM']) && $attr['OPTIMUM']) {
					$optimum = $attr['OPTIMUM'];
				} else {
					$optimum = $min + (($max - $min) / 2);
				}
				if ($optimum < $min) {
					$optimum = $min;
				} else if ($optimum > $max) {
					$optimum = $max;
				}
				if (isset($attr['TYPE']) && $attr['TYPE']) {
					$type = $attr['TYPE'];
				} else {
					$type = '';
				}
				$objattr = array();
				$objattr['margin_top'] = 0;
				$objattr['margin_bottom'] = 0;
				$objattr['margin_left'] = 0;
				$objattr['margin_right'] = 0;
				$objattr['padding_top'] = 0;
				$objattr['padding_bottom'] = 0;
				$objattr['padding_left'] = 0;
				$objattr['padding_right'] = 0;
				$objattr['width'] = 0;
				$objattr['height'] = 0;
				$objattr['border_top']['w'] = 0;
				$objattr['border_bottom']['w'] = 0;
				$objattr['border_left']['w'] = 0;
				$objattr['border_right']['w'] = 0;

				$properties = $this->cssmgr->MergeCSS('INLINE', $tag, $attr);
				if (isset($properties ['DISPLAY']) && strtolower($properties ['DISPLAY']) == 'none') {
					return;
				}
				$objattr['visibility'] = 'visible';
				if (isset($properties['VISIBILITY'])) {
					$v = strtolower($properties['VISIBILITY']);
					if (($v == 'hidden' || $v == 'printonly' || $v == 'screenonly') && $this->visibility == 'visible') {
						$objattr['visibility'] = $v;
					}
				}

				if (isset($properties['MARGIN-TOP'])) {
					$objattr['margin_top'] = $this->ConvertSize($properties['MARGIN-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-BOTTOM'])) {
					$objattr['margin_bottom'] = $this->ConvertSize($properties['MARGIN-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-LEFT'])) {
					$objattr['margin_left'] = $this->ConvertSize($properties['MARGIN-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-RIGHT'])) {
					$objattr['margin_right'] = $this->ConvertSize($properties['MARGIN-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['PADDING-TOP'])) {
					$objattr['padding_top'] = $this->ConvertSize($properties['PADDING-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-BOTTOM'])) {
					$objattr['padding_bottom'] = $this->ConvertSize($properties['PADDING-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-LEFT'])) {
					$objattr['padding_left'] = $this->ConvertSize($properties['PADDING-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-RIGHT'])) {
					$objattr['padding_right'] = $this->ConvertSize($properties['PADDING-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['BORDER-TOP'])) {
					$objattr['border_top'] = $this->border_details($properties['BORDER-TOP']);
				}
				if (isset($properties['BORDER-BOTTOM'])) {
					$objattr['border_bottom'] = $this->border_details($properties['BORDER-BOTTOM']);
				}
				if (isset($properties['BORDER-LEFT'])) {
					$objattr['border_left'] = $this->border_details($properties['BORDER-LEFT']);
				}
				if (isset($properties['BORDER-RIGHT'])) {
					$objattr['border_right'] = $this->border_details($properties['BORDER-RIGHT']);
				}

				if (isset($properties['VERTICAL-ALIGN'])) {
					$objattr['vertical-align'] = $align[strtolower($properties['VERTICAL-ALIGN'])];
				}
				$w = 0;
				$h = 0;
				if (isset($properties['WIDTH']))
					$w = $this->ConvertSize($properties['WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				else if (isset($attr['WIDTH']))
					$w = $this->ConvertSize($attr['WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);

				if (isset($properties['HEIGHT']))
					$h = $this->ConvertSize($properties['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				else if (isset($attr['HEIGHT']))
					$h = $this->ConvertSize($attr['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);

				if (isset($properties['OPACITY']) && $properties['OPACITY'] > 0 && $properties['OPACITY'] <= 1) {
					$objattr['opacity'] = $properties['OPACITY'];
				}
				if ($this->HREF) {
					if (strpos($this->HREF, ".") === false && strpos($this->HREF, "@") !== 0) {
						$href = $this->HREF;
						while (array_key_exists($href, $this->internallink))
							$href = "#" . $href;
						$this->internallink[$href] = $this->AddLink();
						$objattr['link'] = $this->internallink[$href];
					} else {
						$objattr['link'] = $this->HREF;
					}
				}
				$extraheight = $objattr['padding_top'] + $objattr['padding_bottom'] + $objattr['margin_top'] + $objattr['margin_bottom'] + $objattr['border_top']['w'] + $objattr['border_bottom']['w'];
				$extrawidth = $objattr['padding_left'] + $objattr['padding_right'] + $objattr['margin_left'] + $objattr['margin_right'] + $objattr['border_left']['w'] + $objattr['border_right']['w'];

				// Image file
				if (!class_exists('meter', false)) {
					include(_MPDF_PATH . 'classes/meter.php');
				}
				$this->meter = new meter();
				$svg = $this->meter->makeSVG(strtolower($tag), $type, $value, $max, $min, $optimum, $low, $high);
				//Save to local file
				$srcpath = _MPDF_TEMP_PATH . '_tempSVG' . uniqid(rand(1, 100000), true) . '_' . strtolower($tag) . '.svg';
				file_put_contents($srcpath, $svg);
				$orig_srcpath = $srcpath;
				$this->GetFullPath($srcpath);

				$info = $this->_getImage($srcpath, true, true, $orig_srcpath);
				if (!$info) {
					$info = $this->_getImage($this->noImageFile);
					if ($info) {
						$srcpath = $this->noImageFile;
						$w = ($info['w'] * (25.4 / $this->dpi));
						$h = ($info['h'] * (25.4 / $this->dpi));
					}
				}
				if (!$info)
					break;

				$objattr['file'] = $srcpath;
				//Default width and height calculation if needed
				if ($w == 0 and $h == 0) {
					// SVG units are pixels
					$w = $this->FontSize / (10 / _MPDFK) * abs($info['w']) / _MPDFK;
					$h = $this->FontSize / (10 / _MPDFK) * abs($info['h']) / _MPDFK;
				}
				// IF WIDTH OR HEIGHT SPECIFIED
				if ($w == 0)
					$w = abs($h * $info['w'] / $info['h']);
				if ($h == 0)
					$h = abs($w * $info['h'] / $info['w']);

				// Resize to maximum dimensions of page
				$maxWidth = $this->blk[$this->blklvl]['inner_width'];
				$maxHeight = $this->h - ($this->tMargin + $this->bMargin + 1);
				if ($this->fullImageHeight) {
					$maxHeight = $this->fullImageHeight;
				}
				if (($w + $extrawidth) > ($maxWidth + 0.0001)) { // mPDF 5.7.4  0.0001 to allow for rounding errors when w==maxWidth
					$w = $maxWidth - $extrawidth;
					$h = abs($w * $info['h'] / $info['w']);
				}

				if ($h + $extraheight > $maxHeight) {
					$h = $maxHeight - $extraheight;
					$w = abs($h * $info['w'] / $info['h']);
				}
				$objattr['type'] = 'image';
				$objattr['itype'] = $info['type'];

				$objattr['orig_h'] = $info['h'];
				$objattr['orig_w'] = $info['w'];
				$objattr['wmf_x'] = $info['x'];
				$objattr['wmf_y'] = $info['y'];
				$objattr['height'] = $h + $extraheight;
				$objattr['width'] = $w + $extrawidth;
				$objattr['image_height'] = $h;
				$objattr['image_width'] = $w;
				$e = "\xbb\xa4\xactype=image,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
				$properties = array();
				if ($this->tableLevel) {
					$this->_saveCellTextBuffer($e, $this->HREF);
					$this->cell[$this->row][$this->col]['s'] += $objattr['width'];
				} else {
					$this->_saveTextBuffer($e, $this->HREF);
				}

				break;


			case 'BR':
				// Added mPDF 3.0 Float DIV - CLEAR
				if (isset($attr['STYLE'])) {
					$properties = $this->cssmgr->readInlineCSS($attr['STYLE']);
					if (isset($properties['CLEAR'])) {
						$this->ClearFloats(strtoupper($properties['CLEAR']), $this->blklvl);
					} // *CSS-FLOAT*
				}

				// mPDF 6 bidi
				// Inline
				// If unicode-bidi set, any embedding levels, isolates, or overrides started by the inline box are closed at the br and reopened on the other side
				$blockpre = '';
				$blockpost = '';
				if (isset($this->blk[$this->blklvl]['bidicode'])) {
					$blockpre = $this->_setBidiCodes('end', $this->blk[$this->blklvl]['bidicode']);
					$blockpost = $this->_setBidiCodes('start', $this->blk[$this->blklvl]['bidicode']);
				}

				// Inline
				// If unicode-bidi set, any embedding levels, isolates, or overrides started by the inline box are closed at the br and reopened on the other side
				$inlinepre = '';
				$inlinepost = '';
				$iBDF = array();
				if (count($this->InlineBDF)) {
					foreach ($this->InlineBDF AS $k => $ib) {
						foreach ($ib AS $ib2) {
							$iBDF[$ib2[1]] = $ib2[0];
						}
					}
					if (count($iBDF)) {
						ksort($iBDF);
						for ($i = count($iBDF) - 1; $i >= 0; $i--) {
							$inlinepre .= $this->_setBidiCodes('end', $iBDF[$i]);
						}
						for ($i = 0; $i < count($iBDF); $i++) {
							$inlinepost .= $this->_setBidiCodes('start', $iBDF[$i]);
						}
					}
				}

				/* -- TABLES -- */
				if ($this->tableLevel) {

					if ($this->blockjustfinished) {
						$this->_saveCellTextBuffer($blockpre . $inlinepre . "\n" . $inlinepost . $blockpost);
					}

					$this->_saveCellTextBuffer($blockpre . $inlinepre . "\n" . $inlinepost . $blockpost);
					if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
						$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
					} elseif ($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
						$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
					}
					$this->cell[$this->row][$this->col]['s'] = 0; // reset
				} else {
					/* -- END TABLES -- */
					if (count($this->textbuffer)) {
						$this->textbuffer[count($this->textbuffer) - 1][0] = preg_replace('/ $/', '', $this->textbuffer[count($this->textbuffer) - 1][0]);
						if (!empty($this->textbuffer[count($this->textbuffer) - 1][18])) {
							$this->otl->trimOTLdata($this->textbuffer[count($this->textbuffer) - 1][18], false, true);
						} // *OTL*
					}
					$this->_saveTextBuffer($blockpre . $inlinepre . "\n" . $inlinepost . $blockpost);
				} // *TABLES*
				$this->ignorefollowingspaces = true;
				$this->blockjustfinished = false;

				$this->linebreakjustfinished = true;
				break;


			// *********** BLOCKS  ********************


			case 'PRE':
				$this->ispre = true; // ADDED - Prevents left trim of textbuffer in printbuffer()

			case 'DIV':
			case 'FORM':
			case 'CENTER':

			case 'BLOCKQUOTE':
			case 'ADDRESS':

			case 'CAPTION':
			case 'P':
			case 'H1':
			case 'H2':
			case 'H3':
			case 'H4':
			case 'H5':
			case 'H6':
			case 'DL':
			case 'DT':
			case 'DD':
			case 'UL': // mPDF 6  Lists
			case 'OL': // mPDF 6
			case 'LI': // mPDF 6
			case 'FIELDSET':
			case 'DETAILS':
			case 'SUMMARY':
			case 'ARTICLE':
			case 'ASIDE':
			case 'FIGURE':
			case 'FIGCAPTION':
			case 'FOOTER':
			case 'HEADER':
			case 'HGROUP':
			case 'NAV':
			case 'SECTION':
			case 'MAIN':
				// mPDF 6  Lists
				$this->lastoptionaltag = '';

				// mPDF 6 bidi
				// Block
				// If unicode-bidi set on current clock, any embedding levels, isolates, or overrides are closed (not inherited)
				if (isset($this->blk[$this->blklvl]['bidicode'])) {
					$blockpost = $this->_setBidiCodes('end', $this->blk[$this->blklvl]['bidicode']);
					if ($blockpost) {
						$this->OTLdata = array();
						if ($this->tableLevel) {
							$this->_saveCellTextBuffer($blockpost);
						} else {
							$this->_saveTextBuffer($blockpost);
						}
					}
				}


				$p = $this->cssmgr->PreviewBlockCSS($tag, $attr);
				if (isset($p['DISPLAY']) && strtolower($p['DISPLAY']) == 'none') {
					$this->blklvl++;
					$this->blk[$this->blklvl]['hide'] = true;
					$this->blk[$this->blklvl]['tag'] = $tag;  // mPDF 6
					return;
				}
				if ($tag == 'CAPTION') {
					// position is written in AdjstHTML
					if (isset($attr['POSITION']) && strtolower($attr['POSITION']) == 'bottom') {
						$divpos = 'B';
					} else {
						$divpos = 'T';
					}
					if (isset($attr['ALIGN']) && strtolower($attr['ALIGN']) == 'bottom') {
						$cappos = 'B';
					} else if (isset($p['CAPTION-SIDE']) && strtolower($p['CAPTION-SIDE']) == 'bottom') {
						$cappos = 'B';
					} else {
						$cappos = 'T';
					}
					if (isset($attr['ALIGN'])) {
						unset($attr['ALIGN']);
					}
					if ($cappos != $divpos) {
						$this->blklvl++;
						$this->blk[$this->blklvl]['hide'] = true;
						$this->blk[$this->blklvl]['tag'] = $tag;  // mPDF 6
						return;
					}
				}

				/* -- FORMS -- */
				if ($tag == 'FORM') {
					if (isset($attr['METHOD']) && strtolower($attr['METHOD']) == 'get') {
						$this->mpdfform->formMethod = 'GET';
					} else {
						$this->mpdfform->formMethod = 'POST';
					}
					if (isset($attr['ACTION'])) {
						$this->mpdfform->formAction = $attr['ACTION'];
					} else {
						$this->mpdfform->formAction = '';
					}
				}
				/* -- END FORMS -- */


				/* -- CSS-POSITION -- */
				if ((isset($p['POSITION']) && (strtolower($p['POSITION']) == 'fixed' || strtolower($p['POSITION']) == 'absolute')) && $this->blklvl == 0) {
					if ($this->inFixedPosBlock) {
						$this->Error("Cannot nest block with position:fixed or position:absolute");
					}
					$this->inFixedPosBlock = true;
					return;
				}
				/* -- END CSS-POSITION -- */
				// Start Block
				$this->ignorefollowingspaces = true;

				if ($this->blockjustfinished && !count($this->textbuffer) && $this->y != $this->tMargin && $this->collapseBlockMargins) {
					$lastbottommargin = $this->lastblockbottommargin;
				} else {
					$lastbottommargin = 0;
				}
				$this->lastblockbottommargin = 0;
				$this->blockjustfinished = false;


				$this->InlineBDF = array(); // mPDF 6
				$this->InlineBDFctr = 0; // mPDF 6
				$this->InlineProperties = array();
				$this->divbegin = true;

				$this->linebreakjustfinished = false;

				/* -- TABLES -- */
				if ($this->tableLevel) {
					// If already something on the line
					if ($this->cell[$this->row][$this->col]['s'] > 0 && !$this->nestedtablejustfinished) {
						$this->_saveCellTextBuffer("\n");
						if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
							$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
						} elseif ($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
							$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
						}
						$this->cell[$this->row][$this->col]['s'] = 0; // reset
					}
					// Cannot set block properties inside table - use Bold to indicate h1-h6
					if ($tag == 'CENTER' && $this->tdbegin) {
						$this->cell[$this->row][$this->col]['a'] = $align['center'];
					}

					$this->InlineProperties['BLOCKINTABLE'] = $this->saveInlineProperties();
					$properties = $this->cssmgr->MergeCSS('', $tag, $attr);
					if (!empty($properties))
						$this->setCSS($properties, 'INLINE');

					// mPDF 6  Lists
					if ($tag == 'UL' || $tag == 'OL') {
						$this->listlvl++;
						if (isset($attr['START'])) {
							$this->listcounter[$this->listlvl] = intval($attr['START']) - 1;
						} else {
							$this->listcounter[$this->listlvl] = 0;
						}
						$this->listitem = array();
						if ($tag == 'OL')
							$this->listtype[$this->listlvl] = 'decimal';
						else if ($tag == 'UL') {
							if ($this->listlvl % 3 == 1)
								$this->listtype[$this->listlvl] = 'disc';
							elseif ($this->listlvl % 3 == 2)
								$this->listtype[$this->listlvl] = 'circle';
							else
								$this->listtype[$this->listlvl] = 'square';
						}
					}

					// mPDF 6  Lists - in Tables
					if ($tag == 'LI') {
						if ($this->listlvl == 0) { //in case of malformed HTML code. Example:(...)</p><li>Content</li><p>Paragraph1</p>(...)
							$this->listlvl++; // first depth level
							$this->listcounter[$this->listlvl] = 0;
						}
						$this->listcounter[$this->listlvl] ++;
						$this->listitem = array();
						//if in table - output here as a tabletextbuffer
						//position:inside OR position:outside (always output in table as position:inside)
						switch ($this->listtype[$this->listlvl]) {
							case 'upper-alpha':
							case 'upper-latin':
							case 'A':
								$blt = $this->dec2alpha($this->listcounter[$this->listlvl], true) . $this->list_number_suffix;
								break;
							case 'lower-alpha':
							case 'lower-latin':
							case 'a':
								$blt = $this->dec2alpha($this->listcounter[$this->listlvl], false) . $this->list_number_suffix;
								break;
							case 'upper-roman':
							case 'I':
								$blt = $this->dec2roman($this->listcounter[$this->listlvl], true) . $this->list_number_suffix;
								break;
							case 'lower-roman':
							case 'i':
								$blt = $this->dec2roman($this->listcounter[$this->listlvl], false) . $this->list_number_suffix;
								break;
							case 'decimal':
							case '1':
								$blt = $this->listcounter[$this->listlvl] . $this->list_number_suffix;
								break;
							default:
								if ($this->listlvl % 3 == 1 && $this->_charDefined($this->CurrentFont['cw'], 8226)) {
									$blt = "\xe2\x80\xa2";
								}  // &#8226;
								else if ($this->listlvl % 3 == 2 && $this->_charDefined($this->CurrentFont['cw'], 9900)) {
									$blt = "\xe2\x9a\xac";
								} // &#9900;
								else if ($this->listlvl % 3 == 0 && $this->_charDefined($this->CurrentFont['cw'], 9642)) {
									$blt = "\xe2\x96\xaa";
								} // &#9642;
								else {
									$blt = '-';
								}
								break;
						}

						// change to &nbsp; spaces
						if ($this->usingCoreFont) {
							$ls = str_repeat(chr(160) . chr(160), ($this->listlvl - 1) * 2) . $blt . ' ';
						} else {
							$ls = str_repeat("\xc2\xa0\xc2\xa0", ($this->listlvl - 1) * 2) . $blt . ' ';
						}
						$this->_saveCellTextBuffer($ls);
						$this->cell[$this->row][$this->col]['s'] += $this->GetStringWidth($ls);
					}

					break;
				}
				/* -- END TABLES -- */

				if ($this->lastblocklevelchange == 1) {
					$blockstate = 1;
				} // Top margins/padding only
				else if ($this->lastblocklevelchange < 1) {
					$blockstate = 0;
				} // NO margins/padding
				$this->printbuffer($this->textbuffer, $blockstate);
				$this->textbuffer = array();

				$save_blklvl = $this->blklvl;
				$save_blk = $this->blk;

				$this->Reset();

				$pagesel = '';
				/* -- CSS-PAGE -- */
				if (isset($p['PAGE'])) {
					$pagesel = $p['PAGE'];
				}  // mPDF 6 (uses $p - preview of properties so blklvl can be incremented after page-break)
				/* -- END CSS-PAGE -- */

				// If page-box has changed AND/OR PAGE-BREAK-BEFORE
				// mPDF 6 (uses $p - preview of properties so blklvl can be imcremented after page-break)
				if (!$this->tableLevel && (($pagesel && (!isset($this->page_box['current']) || $pagesel != $this->page_box['current'])) || (isset($p['PAGE-BREAK-BEFORE']) && $p['PAGE-BREAK-BEFORE']))) {
					// mPDF 6 pagebreaktype
					$startpage = $this->page;
					$pagebreaktype = $this->defaultPagebreakType;
					$this->lastblocklevelchange = -1;
					if ($this->ColActive) {
						$pagebreaktype = 'cloneall';
					}
					if ($pagesel && (!isset($this->page_box['current']) || $pagesel != $this->page_box['current'])) {
						$pagebreaktype = 'cloneall';
					}
					$this->_preForcedPagebreak($pagebreaktype);

					if (isset($p['PAGE-BREAK-BEFORE'])) {
						if (strtoupper($p['PAGE-BREAK-BEFORE']) == 'RIGHT') {
							$this->AddPage($this->CurOrientation, 'NEXT-ODD', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, $pagesel);
						} else if (strtoupper($p['PAGE-BREAK-BEFORE']) == 'LEFT') {
							$this->AddPage($this->CurOrientation, 'NEXT-EVEN', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, $pagesel);
						} else if (strtoupper($p['PAGE-BREAK-BEFORE']) == 'ALWAYS') {
							$this->AddPage($this->CurOrientation, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, $pagesel);
						} else if ($this->page_box['current'] != $pagesel) {
							$this->AddPage($this->CurOrientation, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, $pagesel);
						} // *CSS-PAGE*
					}
					/* -- CSS-PAGE -- */
					// Must Add new page if changed page properties
					else if (!isset($this->page_box['current']) || $pagesel != $this->page_box['current']) {
						$this->AddPage($this->CurOrientation, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, $pagesel);
					}
					/* -- END CSS-PAGE -- */

					// mPDF 6 pagebreaktype
					$this->_postForcedPagebreak($pagebreaktype, $startpage, $save_blk, $save_blklvl);
				}

				// mPDF 6 pagebreaktype - moved after pagebreak
				$this->blklvl++;
				$currblk = & $this->blk[$this->blklvl];
				$this->initialiseBlock($currblk);
				$prevblk = & $this->blk[$this->blklvl - 1];
				$currblk['tag'] = $tag;
				$currblk['attr'] = $attr;

				$properties = $this->cssmgr->MergeCSS('BLOCK', $tag, $attr); // mPDF 6 - moved to after page-break-before
				// mPDF 6 page-break-inside:avoid
				if (isset($properties['PAGE-BREAK-INSIDE']) && strtoupper($properties['PAGE-BREAK-INSIDE']) == 'AVOID' && !$this->ColActive && !$this->keep_block_together && !isset($attr['PAGEBREAKAVOIDCHECKED'])) { // avoid re-iterating using PAGEBREAKAVOIDCHECKED; set in CloseTag
					$currblk['keep_block_together'] = 1;
					$currblk['array_i'] = $ihtml; // mPDF 6
					$this->kt_y00 = $this->y;
					$this->kt_p00 = $this->page;
					$this->keep_block_together = 1;
				}
				if ($lastbottommargin && isset($properties['MARGIN-TOP']) && $properties['MARGIN-TOP'] && empty($properties['FLOAT'])) {
					$currblk['lastbottommargin'] = $lastbottommargin;
				}

				if (isset($properties['Z-INDEX']) && $this->current_layer == 0) {
					$v = intval($properties['Z-INDEX']);
					if ($v > 0) {
						$currblk['z-index'] = $v;
						$this->BeginLayer($v);
					}
				}


				// mPDF 6  Lists
				// List-type set by attribute
				if ($tag == 'OL' || $tag == 'UL' || $tag == 'LI') {
					if (isset($attr['TYPE']) && $attr['TYPE']) {
						$listtype = $attr['TYPE'];
						switch ($listtype) {
							case 'A':
								$listtype = 'upper-latin';
								break;
							case 'a':
								$listtype = 'lower-latin';
								break;
							case 'I':
								$listtype = 'upper-roman';
								break;
							case 'i':
								$listtype = 'lower-roman';
								break;
							case '1':
								$listtype = 'decimal';
								break;
						}
						$currblk['list_style_type'] = $listtype;
					}
				}

				$this->setCSS($properties, 'BLOCK', $tag); //name(id/class/style) found in the CSS array!
				$currblk['InlineProperties'] = $this->saveInlineProperties();

				if (isset($properties['VISIBILITY'])) {
					$v = strtolower($properties['VISIBILITY']);
					if (($v == 'hidden' || $v == 'printonly' || $v == 'screenonly') && $this->visibility == 'visible' && !$this->tableLevel) {
						$currblk['visibility'] = $v;
						$this->SetVisibility($v);
					}
				}

				// mPDF 6
				if (isset($attr['ALIGN']) && $attr['ALIGN']) {
					$currblk['block-align'] = $align[strtolower($attr['ALIGN'])];
				}


				if (isset($properties['HEIGHT'])) {
					$currblk['css_set_height'] = $this->ConvertSize($properties['HEIGHT'], ($this->h - $this->tMargin - $this->bMargin), $this->FontSize, false);
					if (($currblk['css_set_height'] + $this->y) > $this->PageBreakTrigger && $this->y > $this->tMargin + 5 && $currblk['css_set_height'] < ($this->h - ($this->tMargin + $this->bMargin))) {
						$this->AddPage($this->CurOrientation);
					}
				} else {
					$currblk['css_set_height'] = false;
				}


				// Added mPDF 3.0 Float DIV
				if (isset($prevblk['blockContext'])) {
					$currblk['blockContext'] = $prevblk['blockContext'];
				} // *CSS-FLOAT*

				if (isset($properties['CLEAR'])) {
					$this->ClearFloats(strtoupper($properties['CLEAR']), $this->blklvl - 1);
				} // *CSS-FLOAT*

				$container_w = $prevblk['inner_width'];
				$bdr = $currblk['border_right']['w'];
				$bdl = $currblk['border_left']['w'];
				$pdr = $currblk['padding_right'];
				$pdl = $currblk['padding_left'];

				if (isset($currblk['css_set_width'])) {
					$setwidth = $currblk['css_set_width'];
				} else {
					$setwidth = 0;
				}

				/* -- CSS-FLOAT -- */
				if (isset($properties['FLOAT']) && strtoupper($properties['FLOAT']) == 'RIGHT' && !$this->ColActive) {
					// Cancel Keep-Block-together
					$currblk['keep_block_together'] = false;
					$this->kt_y00 = '';
					$this->keep_block_together = 0;

					$this->blockContext++;
					$currblk['blockContext'] = $this->blockContext;

					list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl - 1);

					// DIV is too narrow for text to fit!
					$maxw = $container_w - $l_width - $r_width;
					if (($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) > $maxw || ($maxw - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) < (2 * $this->GetCharWidth('W', false))) {
						// Too narrow to fit - try to move down past L or R float
						if ($l_max < $r_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $r_width) && (($container_w - $r_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > (2 * $this->GetCharWidth('W', false))) {
							$this->ClearFloats('LEFT', $this->blklvl - 1);
						} else if ($r_max < $l_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $l_width) && (($container_w - $l_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > (2 * $this->GetCharWidth('W', false))) {
							$this->ClearFloats('RIGHT', $this->blklvl - 1);
						} else {
							$this->ClearFloats('BOTH', $this->blklvl - 1);
						}
						list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl - 1);
					}

					if ($r_exists) {
						$currblk['margin_right'] += $r_width;
					}

					$currblk['float'] = 'R';
					$currblk['float_start_y'] = $this->y;
					if ($currblk['css_set_width']) {
						$currblk['margin_left'] = $container_w - ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_right']);
						$currblk['float_width'] = ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_right']);
					} else {
						// *** If no width set - would need to buffer and keep track of max width, then Right-align if not full width
						// and do borders and backgrounds - For now - just set to maximum width left

						if ($l_exists) {
							$currblk['margin_left'] += $l_width;
						}
						$currblk['css_set_width'] = $container_w - ($currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr);

						$currblk['float_width'] = ($currblk['css_set_width'] + $bdl + $pdl + $bdr + $pdr + $currblk['margin_right']);
					}
				} else if (isset($properties['FLOAT']) && strtoupper($properties['FLOAT']) == 'LEFT' && !$this->ColActive) {
					// Cancel Keep-Block-together
					$currblk['keep_block_together'] = false;
					$this->kt_y00 = '';
					$this->keep_block_together = 0;

					$this->blockContext++;
					$currblk['blockContext'] = $this->blockContext;

					list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl - 1);

					// DIV is too narrow for text to fit!
					$maxw = $container_w - $l_width - $r_width;
					if (($setwidth + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr) > $maxw || ($maxw - ($currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) < (2 * $this->GetCharWidth('W', false))) {
						// Too narrow to fit - try to move down past L or R float
						if ($l_max < $r_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $r_width) && (($container_w - $r_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > (2 * $this->GetCharWidth('W', false))) {
							$this->ClearFloats('LEFT', $this->blklvl - 1);
						} else if ($r_max < $l_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $l_width) && (($container_w - $l_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > (2 * $this->GetCharWidth('W', false))) {
							$this->ClearFloats('RIGHT', $this->blklvl - 1);
						} else {
							$this->ClearFloats('BOTH', $this->blklvl - 1);
						}
						list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl - 1);
					}

					if ($l_exists) {
						$currblk['margin_left'] += $l_width;
					}

					$currblk['float'] = 'L';
					$currblk['float_start_y'] = $this->y;
					if ($setwidth) {
						$currblk['margin_right'] = $container_w - ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_left']);
						$currblk['float_width'] = ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_left']);
					} else {
						// *** If no width set - would need to buffer and keep track of max width, then Right-align if not full width
						// and do borders and backgrounds - For now - just set to maximum width left

						if ($r_exists) {
							$currblk['margin_right'] += $r_width;
						}
						$currblk['css_set_width'] = $container_w - ($currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr);

						$currblk['float_width'] = ($currblk['css_set_width'] + $bdl + $pdl + $bdr + $pdr + $currblk['margin_left']);
					}
				} else {
					// Don't allow overlap - if floats present - adjust padding to avoid overlap with Floats
					list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl - 1);
					$maxw = $container_w - $l_width - $r_width;
					if (($setwidth + $currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) > $maxw || ($maxw - ($currblk['margin_right'] + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) < (2 * $this->GetCharWidth('W', false))) {
						// Too narrow to fit - try to move down past L or R float
						if ($l_max < $r_max && ($setwidth + $currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $r_width) && (($container_w - $r_width) - ($currblk['margin_right'] + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) > (2 * $this->GetCharWidth('W', false))) {
							$this->ClearFloats('LEFT', $this->blklvl - 1);
						} else if ($r_max < $l_max && ($setwidth + $currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $l_width) && (($container_w - $l_width) - ($currblk['margin_right'] + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) > (2 * $this->GetCharWidth('W', false))) {
							$this->ClearFloats('RIGHT', $this->blklvl - 1);
						} else {
							$this->ClearFloats('BOTH', $this->blklvl - 1);
						}
						list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl - 1);
					}
					if ($r_exists) {
						$currblk['padding_right'] = max(($r_width - $currblk['margin_right'] - $bdr), $pdr);
					}
					if ($l_exists) {
						$currblk['padding_left'] = max(($l_width - $currblk['margin_left'] - $bdl), $pdl);
					}
				}
				/* -- END CSS-FLOAT -- */


				/* -- BORDER-RADIUS -- */
				// Automatically increase padding if required for border-radius
				if ($this->autoPadding && !$this->ColActive) {
					if ($currblk['border_radius_TL_H'] > $currblk['padding_left'] && $currblk['border_radius_TL_V'] > $currblk['padding_top']) {
						if ($currblk['border_radius_TL_H'] > $currblk['border_radius_TL_V']) {
							$this->_borderPadding($currblk['border_radius_TL_H'], $currblk['border_radius_TL_V'], $currblk['padding_left'], $currblk['padding_top']);
						} else {
							$this->_borderPadding($currblk['border_radius_TL_V'], $currblk['border_radius_TL_H'], $currblk['padding_top'], $currblk['padding_left']);
						}
					}
					if ($currblk['border_radius_TR_H'] > $currblk['padding_right'] && $currblk['border_radius_TR_V'] > $currblk['padding_top']) {
						if ($currblk['border_radius_TR_H'] > $currblk['border_radius_TR_V']) {
							$this->_borderPadding($currblk['border_radius_TR_H'], $currblk['border_radius_TR_V'], $currblk['padding_right'], $currblk['padding_top']);
						} else {
							$this->_borderPadding($currblk['border_radius_TR_V'], $currblk['border_radius_TR_H'], $currblk['padding_top'], $currblk['padding_right']);
						}
					}
					if ($currblk['border_radius_BL_H'] > $currblk['padding_left'] && $currblk['border_radius_BL_V'] > $currblk['padding_bottom']) {
						if ($currblk['border_radius_BL_H'] > $currblk['border_radius_BL_V']) {
							$this->_borderPadding($currblk['border_radius_BL_H'], $currblk['border_radius_BL_V'], $currblk['padding_left'], $currblk['padding_bottom']);
						} else {
							$this->_borderPadding($currblk['border_radius_BL_V'], $currblk['border_radius_BL_H'], $currblk['padding_bottom'], $currblk['padding_left']);
						}
					}
					if ($currblk['border_radius_BR_H'] > $currblk['padding_right'] && $currblk['border_radius_BR_V'] > $currblk['padding_bottom']) {
						if ($currblk['border_radius_BR_H'] > $currblk['border_radius_BR_V']) {
							$this->_borderPadding($currblk['border_radius_BR_H'], $currblk['border_radius_BR_V'], $currblk['padding_right'], $currblk['padding_bottom']);
						} else {
							$this->_borderPadding($currblk['border_radius_BR_V'], $currblk['border_radius_BR_H'], $currblk['padding_bottom'], $currblk['padding_right']);
						}
					}
				}
				/* -- END BORDER-RADIUS -- */


				// Hanging indent - if negative indent: ensure padding is >= indent
				if (!isset($currblk['text_indent'])) {
					$currblk['text_indent'] = null;
				}
				if (!isset($currblk['inner_width'])) {
					$currblk['inner_width'] = null;
				}
				$cbti = $this->ConvertSize($currblk['text_indent'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				if ($cbti < 0) {
					$hangind = -($cbti);
					if (isset($currblk['direction']) && $currblk['direction'] == 'rtl') { // *OTL*
						$currblk['padding_right'] = max($currblk['padding_right'], $hangind); // *OTL*
					} // *OTL*
					else { // *OTL*
						$currblk['padding_left'] = max($currblk['padding_left'], $hangind);
					} // *OTL*
				}

				if (isset($currblk['css_set_width'])) {
					if (isset($properties['MARGIN-LEFT']) && isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-LEFT']) == 'auto' && strtolower($properties['MARGIN-RIGHT']) == 'auto') {
						// Try to reduce margins to accomodate - if still too wide, set margin-right/left=0 (reduces width)
						$anyextra = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right']);
						if ($anyextra > 0) {
							$currblk['margin_left'] = $currblk['margin_right'] = $anyextra / 2;
						} else {
							$currblk['margin_left'] = $currblk['margin_right'] = 0;
						}
					} else if (isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT']) == 'auto') {
						// Try to reduce margin-left to accomodate - if still too wide, set margin-left=0 (reduces width)
						$currblk['margin_left'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_right']);
						if ($currblk['margin_left'] < 0) {
							$currblk['margin_left'] = 0;
						}
					} else if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT']) == 'auto') {
						// Try to reduce margin-right to accomodate - if still too wide, set margin-right=0 (reduces width)
						$currblk['margin_right'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_left']);
						if ($currblk['margin_right'] < 0) {
							$currblk['margin_right'] = 0;
						}
					} else {
						if ($currblk['direction'] == 'rtl') { // *OTL*
							// Try to reduce margin-left to accomodate - if still too wide, set margin-left=0 (reduces width)
							$currblk['margin_left'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_right']); // *OTL*
							if ($currblk['margin_left'] < 0) { // *OTL*
								$currblk['margin_left'] = 0; // *OTL*
							} // *OTL*
						} // *OTL*
						else { // *OTL*
							// Try to reduce margin-right to accomodate - if still too wide, set margin-right=0 (reduces width)
							$currblk['margin_right'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_left']);
							if ($currblk['margin_right'] < 0) {
								$currblk['margin_right'] = 0;
							}
						} // *OTL*
					}
				}

				$currblk['outer_left_margin'] = $prevblk['outer_left_margin'] + $currblk['margin_left'] + $prevblk['border_left']['w'] + $prevblk['padding_left'];
				$currblk['outer_right_margin'] = $prevblk['outer_right_margin'] + $currblk['margin_right'] + $prevblk['border_right']['w'] + $prevblk['padding_right'];

				$currblk['width'] = $this->pgwidth - ($currblk['outer_right_margin'] + $currblk['outer_left_margin']);
				$currblk['inner_width'] = $currblk['width'] - ($currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right']);

				// Check DIV is not now too narrow to fit text
				$mw = 2 * $this->GetCharWidth('W', false);
				if ($currblk['inner_width'] < $mw) {
					$currblk['padding_left'] = 0;
					$currblk['padding_right'] = 0;
					$currblk['border_left']['w'] = 0.2;
					$currblk['border_right']['w'] = 0.2;
					$currblk['margin_left'] = 0;
					$currblk['margin_right'] = 0;
					$currblk['outer_left_margin'] = $prevblk['outer_left_margin'] + $currblk['margin_left'] + $prevblk['border_left']['w'] + $prevblk['padding_left'];
					$currblk['outer_right_margin'] = $prevblk['outer_right_margin'] + $currblk['margin_right'] + $prevblk['border_right']['w'] + $prevblk['padding_right'];
					$currblk['width'] = $this->pgwidth - ($currblk['outer_right_margin'] + $currblk['outer_left_margin']);
					$currblk['inner_width'] = $this->pgwidth - ($currblk['outer_right_margin'] + $currblk['outer_left_margin'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right']);
//		if ($currblk['inner_width'] < $mw) { $this->Error("DIV is too narrow for text to fit!"); }
				}

				$this->x = $this->lMargin + $currblk['outer_left_margin'];

				/* -- BACKGROUNDS -- */
				if (isset($properties['BACKGROUND-IMAGE']) && $properties['BACKGROUND-IMAGE'] && !$this->kwt && !$this->ColActive && !$this->keep_block_together) {
					$ret = $this->SetBackground($properties, $currblk['inner_width']);
					if ($ret) {
						$currblk['background-image'] = $ret;
					}
				}
				/* -- END BACKGROUNDS -- */

				/* -- TABLES -- */
				if ($this->use_kwt && isset($attr['KEEP-WITH-TABLE']) && !$this->ColActive && !$this->keep_block_together) {
					$this->kwt = true;
					$this->kwt_y0 = $this->y;
					//$this->kwt_x0 = $this->x;
					$this->kwt_x0 = $this->lMargin; // mPDF 6
					$this->kwt_height = 0;
					$this->kwt_buffer = array();
					$this->kwt_Links = array();
					$this->kwt_Annots = array();
					$this->kwt_moved = false;
					$this->kwt_saved = false;
					$this->kwt_Reference = array();
					$this->kwt_BMoutlines = array();
					$this->kwt_toc = array();
				} else {
					/* -- END TABLES -- */
					$this->kwt = false;
				} // *TABLES*
				//Save x,y coords in case we need to print borders...
				$currblk['y0'] = $this->y;
				$currblk['initial_y0'] = $this->y; // mPDF 6
				$currblk['x0'] = $this->x;
				$currblk['initial_x0'] = $this->x; // mPDF 6
				$currblk['initial_startpage'] = $this->page;
				$currblk['startpage'] = $this->page; // mPDF 6
				$this->oldy = $this->y;

				$this->lastblocklevelchange = 1;


				// mPDF 6  Lists
				if ($tag == 'OL' || $tag == 'UL') {
					$this->listlvl++;
					if (isset($attr['START']) && $attr['START']) {
						$this->listcounter[$this->listlvl] = intval($attr['START']) - 1;
					} else {
						$this->listcounter[$this->listlvl] = 0;
					}
					$this->listitem = array();

					// List-type
					if (!isset($currblk['list_style_type']) || !$currblk['list_style_type']) {
						if ($tag == 'OL')
							$currblk['list_style_type'] = 'decimal';
						else if ($tag == 'UL') {
							if ($this->listlvl % 3 == 1)
								$currblk['list_style_type'] = 'disc';
							elseif ($this->listlvl % 3 == 2)
								$currblk['list_style_type'] = 'circle';
							else
								$currblk['list_style_type'] = 'square';
						}
					}

					// List-image
					if (!isset($currblk['list_style_image']) || !$currblk['list_style_image']) {
						$currblk['list_style_image'] = 'none';
					}

					// List-position
					if (!isset($currblk['list_style_position']) || !$currblk['list_style_position']) {
						$currblk['list_style_position'] = 'outside';
					}

					// Default indentation using padding
					if (strtolower($this->list_auto_mode) == 'mpdf' && isset($currblk['list_style_position']) && $currblk['list_style_position'] == 'outside' && isset($currblk['list_style_image']) && $currblk['list_style_image'] == 'none' && (!isset($currblk['list_style_type']) || !preg_match('/U\+([a-fA-F0-9]+)/i', $currblk['list_style_type']))) {
						$autopadding = $this->_getListMarkerWidth($currblk, $ahtml, $ihtml);
						if ($this->listlvl > 1 || $this->list_indent_first_level) {
							$autopadding += $this->ConvertSize($this->list_indent_default_mpdf, $currblk['inner_width'], $this->FontSize, false);
						}
						// autopadding value is applied to left or right according
						// to dir of block. Once a CSS value is set for padding it overrides this default value.
						if (isset($properties['PADDING-RIGHT']) && $properties['PADDING-RIGHT'] == 'auto' && isset($currblk['direction']) && $currblk['direction'] == 'rtl') {
							$currblk['padding_right'] = $autopadding;
						} else if (isset($properties['PADDING-LEFT']) && $properties['PADDING-LEFT'] == 'auto') {
							$currblk['padding_left'] = $autopadding;
						}
					} else {
						// Initial default value is set by $this->list_indent_default in config.php; this value is applied to left or right according
						// to dir of block. Once a CSS value is set for padding it overrides this default value.
						if (isset($properties['PADDING-RIGHT']) && $properties['PADDING-RIGHT'] == 'auto' && isset($currblk['direction']) && $currblk['direction'] == 'rtl') {
							$currblk['padding_right'] = $this->ConvertSize($this->list_indent_default, $currblk['inner_width'], $this->FontSize, false);
						} else if (isset($properties['PADDING-LEFT']) && $properties['PADDING-LEFT'] == 'auto') {
							$currblk['padding_left'] = $this->ConvertSize($this->list_indent_default, $currblk['inner_width'], $this->FontSize, false);
						}
					}
				}


				// mPDF 6  Lists
				if ($tag == 'LI') {
					if ($this->listlvl == 0) { //in case of malformed HTML code. Example:(...)</p><li>Content</li><p>Paragraph1</p>(...)
						$this->listlvl++; // first depth level
						$this->listcounter[$this->listlvl] = 0;
					}
					$this->listcounter[$this->listlvl] ++;
					$this->listitem = array();

					// Listitem-type
					$this->_setListMarker($currblk['list_style_type'], $currblk['list_style_image'], $currblk['list_style_position']);
				}

				// mPDF 6 Bidirectional formatting for block elements
				$bdf = false;
				$bdf2 = '';
				$popd = '';

				// Get current direction
				if (isset($currblk['direction'])) {
					$currdir = $currblk['direction'];
				} else {
					$currdir = 'ltr';
				}
				if (isset($attr['DIR']) and $attr['DIR'] != '') {
					$currdir = strtolower($attr['DIR']);
				}
				if (isset($properties['DIRECTION'])) {
					$currdir = strtolower($properties['DIRECTION']);
				}

				// mPDF 6 bidi
				// cf. http://www.w3.org/TR/css3-writing-modes/#unicode-bidi
				if (isset($properties ['UNICODE-BIDI']) && (strtolower($properties ['UNICODE-BIDI']) == 'bidi-override' || strtolower($properties ['UNICODE-BIDI']) == 'isolate-override')) {
					if ($currdir == 'rtl') {
						$bdf = 0x202E;
						$popd = 'RLOPDF';
					} // U+202E RLO
					else {
						$bdf = 0x202D;
						$popd = 'LROPDF';
					} // U+202D LRO
				} else if (isset($properties ['UNICODE-BIDI']) && strtolower($properties ['UNICODE-BIDI']) == 'plaintext') {
					$bdf = 0x2068;
					$popd = 'FSIPDI'; // U+2068 FSI
				}
				if ($bdf) {
					if ($bdf2) {
						$bdf2 = code2utf($bdf);
					}
					$this->OTLdata = array();
					if ($this->tableLevel) {
						$this->_saveCellTextBuffer(code2utf($bdf) . $bdf2);
					} else {
						$this->_saveTextBuffer(code2utf($bdf) . $bdf2);
					}
					$this->biDirectional = true;
					$currblk['bidicode'] = $popd;
				}

				break;

			case 'HR':
				// Added mPDF 3.0 Float DIV - CLEAR
				if (isset($attr['STYLE'])) {
					$properties = $this->cssmgr->readInlineCSS($attr['STYLE']);
					if (isset($properties['CLEAR'])) {
						$this->ClearFloats(strtoupper($properties['CLEAR']), $this->blklvl);
					} // *CSS-FLOAT*
				}

				$this->ignorefollowingspaces = true;

				$objattr = array();
				$objattr['margin_top'] = 0;
				$objattr['margin_bottom'] = 0;
				$objattr['margin_left'] = 0;
				$objattr['margin_right'] = 0;
				$objattr['width'] = 0;
				$objattr['height'] = 0;
				$objattr['border_top']['w'] = 0;
				$objattr['border_bottom']['w'] = 0;
				$objattr['border_left']['w'] = 0;
				$objattr['border_right']['w'] = 0;
				$properties = $this->cssmgr->MergeCSS('', $tag, $attr);
				if (isset($properties['MARGIN-TOP'])) {
					$objattr['margin_top'] = $this->ConvertSize($properties['MARGIN-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-BOTTOM'])) {
					$objattr['margin_bottom'] = $this->ConvertSize($properties['MARGIN-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['WIDTH'])) {
					$objattr['width'] = $this->ConvertSize($properties['WIDTH'], $this->blk[$this->blklvl]['inner_width']);
				} else if (isset($attr['WIDTH']) && $attr['WIDTH'] != '')
					$objattr['width'] = $this->ConvertSize($attr['WIDTH'], $this->blk[$this->blklvl]['inner_width']);
				if (isset($properties['TEXT-ALIGN'])) {
					$objattr['align'] = $align[strtolower($properties['TEXT-ALIGN'])];
				} else if (isset($attr['ALIGN']) && $attr['ALIGN'] != '')
					$objattr['align'] = $align[strtolower($attr['ALIGN'])];

				if (isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT']) == 'auto') {
					$objattr['align'] = 'R';
				}
				if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT']) == 'auto') {
					$objattr['align'] = 'L';
					if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT']) == 'auto' && isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT']) == 'auto') {
						$objattr['align'] = 'C';
					}
				}
				if (isset($properties['COLOR'])) {
					$objattr['color'] = $this->ConvertColor($properties['COLOR']);
				} else if (isset($attr['COLOR']) && $attr['COLOR'] != '')
					$objattr['color'] = $this->ConvertColor($attr['COLOR']);
				if (isset($properties['HEIGHT'])) {
					$objattr['linewidth'] = $this->ConvertSize($properties['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}


				


			// *********** FORM ELEMENTS ********************

			/* -- FORMS -- */
			case 'SELECT':
				$this->lastoptionaltag = ''; // Save current HTML specified optional endtag
				$this->InlineProperties[$tag] = $this->saveInlineProperties();
				$properties = $this->cssmgr->MergeCSS('', $tag, $attr);
				if (isset($properties['FONT-FAMILY'])) {
					$this->SetFont($properties['FONT-FAMILY'], $this->FontStyle, 0, false);
				}
				if (isset($properties['FONT-SIZE'])) {
					$mmsize = $this->ConvertSize($properties['FONT-SIZE'], $this->default_font_size / _MPDFK);
					$this->SetFontSize($mmsize * _MPDFK, false);
				}
				if (isset($attr['SPELLCHECK']) && strtolower($attr['SPELLCHECK']) == 'true') {
					$this->selectoption['SPELLCHECK'] = true;
				}

				if (isset($properties['COLOR'])) {
					$this->selectoption['COLOR'] = $this->ConvertColor($properties['COLOR']);
				}
				$this->specialcontent = "type=select";
				if (isset($attr['DISABLED'])) {
					$this->selectoption['DISABLED'] = $attr['DISABLED'];
				}
				if (isset($attr['READONLY'])) {
					$this->selectoption['READONLY'] = $attr['READONLY'];
				}
				if (isset($attr['REQUIRED'])) {
					$this->selectoption['REQUIRED'] = $attr['REQUIRED'];
				}
				if (isset($attr['EDITABLE'])) {
					$this->selectoption['EDITABLE'] = $attr['EDITABLE'];
				}
				if (isset($attr['TITLE'])) {
					$this->selectoption['TITLE'] = $attr['TITLE'];
				}
				if (isset($attr['MULTIPLE'])) {
					$this->selectoption['MULTIPLE'] = $attr['MULTIPLE'];
				}
				if (isset($attr['SIZE']) && $attr['SIZE'] > 1) {
					$this->selectoption['SIZE'] = $attr['SIZE'];
				}
				if ($this->useActiveForms) {
					if (isset($attr['NAME'])) {
						$this->selectoption['NAME'] = $attr['NAME'];
					}
					if (isset($attr['ONCHANGE'])) {
						$this->selectoption['ONCHANGE'] = $attr['ONCHANGE'];
					}
				}

				$properties = array();
				break;

			case 'OPTION':
				$this->lastoptionaltag = '';
				$this->selectoption['ACTIVE'] = true;
				$this->selectoption['currentSEL'] = false;
				if (empty($this->selectoption)) {
					$this->selectoption['MAXWIDTH'] = '';
					$this->selectoption['SELECTED'] = '';
				}
				if (isset($attr['SELECTED'])) {
					$this->selectoption['SELECTED'] = '';
					$this->selectoption['currentSEL'] = true;
				}
				if (isset($attr['VALUE'])) {
					$attr['VALUE'] = strcode2utf($attr['VALUE']);
					$attr['VALUE'] = $this->lesser_entity_decode($attr['VALUE']);
					if ($this->onlyCoreFonts)
						$attr['VALUE'] = mb_convert_encoding($attr['VALUE'], $this->mb_enc, 'UTF-8');
				}
				$this->selectoption['currentVAL'] = $attr['VALUE'];
				break;

			case 'TEXTAREA':
				$objattr = array();
				$objattr['margin_top'] = 0;
				$objattr['margin_bottom'] = 0;
				$objattr['margin_left'] = 0;
				$objattr['margin_right'] = 0;
				$objattr['width'] = 0;
				$objattr['height'] = 0;
				$objattr['border_top']['w'] = 0;
				$objattr['border_bottom']['w'] = 0;
				$objattr['border_left']['w'] = 0;
				$objattr['border_right']['w'] = 0;
				if (isset($attr['DISABLED'])) {
					$objattr['disabled'] = true;
				}
				if (isset($attr['READONLY'])) {
					$objattr['readonly'] = true;
				}
				if (isset($attr['REQUIRED'])) {
					$objattr['required'] = true;
				}
				if (isset($attr['SPELLCHECK']) && strtolower($attr['SPELLCHECK']) == 'true') {
					$objattr['spellcheck'] = true;
				}
				if (isset($attr['TITLE'])) {
					$objattr['title'] = $attr['TITLE'];
					if ($this->onlyCoreFonts)
						$objattr['title'] = mb_convert_encoding($objattr['title'], $this->mb_enc, 'UTF-8');
				}
				if ($this->useActiveForms) {
					if (isset($attr['NAME'])) {
						$objattr['fieldname'] = $attr['NAME'];
					}
					$this->mpdfform->form_element_spacing['textarea']['outer']['v'] = 0;
					$this->mpdfform->form_element_spacing['textarea']['inner']['v'] = 0;
					if (isset($attr['ONCALCULATE'])) {
						$objattr['onCalculate'] = $attr['ONCALCULATE'];
					} else if (isset($attr['ONCHANGE'])) {
						$objattr['onCalculate'] = $attr['ONCHANGE'];
					}
					if (isset($attr['ONVALIDATE'])) {
						$objattr['onValidate'] = $attr['ONVALIDATE'];
					}
					if (isset($attr['ONKEYSTROKE'])) {
						$objattr['onKeystroke'] = $attr['ONKEYSTROKE'];
					}
					if (isset($attr['ONFORMAT'])) {
						$objattr['onFormat'] = $attr['ONFORMAT'];
					}
				}
				$this->InlineProperties[$tag] = $this->saveInlineProperties();
				$properties = $this->cssmgr->MergeCSS('', $tag, $attr);
				if (isset($properties['FONT-FAMILY'])) {
					$this->SetFont($properties['FONT-FAMILY'], '', 0, false);
				}
				if (isset($properties['FONT-SIZE'])) {
					$mmsize = $this->ConvertSize($properties['FONT-SIZE'], $this->default_font_size / _MPDFK);
					$this->SetFontSize($mmsize * _MPDFK, false);
				}
				if (isset($properties['COLOR'])) {
					$objattr['color'] = $this->ConvertColor($properties['COLOR']);
				}
				$objattr['fontfamily'] = $this->FontFamily;
				$objattr['fontsize'] = $this->FontSizePt;
				if ($this->useActiveForms) {
					if (isset($properties['TEXT-ALIGN'])) {
						$objattr['text_align'] = $align[strtolower($properties['TEXT-ALIGN'])];
					} else if (isset($attr['ALIGN'])) {
						$objattr['text_align'] = $align[strtolower($attr['ALIGN'])];
					}
					if (isset($properties['OVERFLOW']) && strtolower($properties['OVERFLOW']) == 'hidden') {
						$objattr['donotscroll'] = true;
					}
					if (isset($properties['BORDER-TOP-COLOR'])) {
						$objattr['border-col'] = $this->ConvertColor($properties['BORDER-TOP-COLOR']);
					}
					if (isset($properties['BACKGROUND-COLOR'])) {
						$objattr['background-col'] = $this->ConvertColor($properties['BACKGROUND-COLOR']);
					}
				}
				$this->SetLineHeight('', $this->mpdfform->textarea_lineheight);

				$w = 0;
				$h = 0;
				if (isset($properties['WIDTH']))
					$w = $this->ConvertSize($properties['WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				if (isset($properties['HEIGHT']))
					$h = $this->ConvertSize($properties['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				if (isset($properties['VERTICAL-ALIGN'])) {
					$objattr['vertical-align'] = $align[strtolower($properties['VERTICAL-ALIGN'])];
				}

				$colsize = 20; //HTML default value
				$rowsize = 2; //HTML default value
				if (isset($attr['COLS']))
					$colsize = intval($attr['COLS']);
				if (isset($attr['ROWS']))
					$rowsize = intval($attr['ROWS']);

				$charsize = $this->GetCharWidth('w', false);
				if ($w) {
					$colsize = round(($w - ($this->mpdfform->form_element_spacing['textarea']['outer']['h'] * 2) - ($this->mpdfform->form_element_spacing['textarea']['inner']['h'] * 2)) / $charsize);
				}
				if ($h) {
					$rowsize = round(($h - ($this->mpdfform->form_element_spacing['textarea']['outer']['v'] * 2) - ($this->mpdfform->form_element_spacing['textarea']['inner']['v'] * 2)) / $this->lineheight);
				}

				$objattr['type'] = 'textarea';
				$objattr['width'] = ($colsize * $charsize) + ($this->mpdfform->form_element_spacing['textarea']['outer']['h'] * 2) + ($this->mpdfform->form_element_spacing['textarea']['inner']['h'] * 2);
				$objattr['height'] = ($rowsize * $this->lineheight) + ($this->mpdfform->form_element_spacing['textarea']['outer']['v'] * 2) + ($this->mpdfform->form_element_spacing['textarea']['inner']['v'] * 2);
				$objattr['rows'] = $rowsize;
				$objattr['cols'] = $colsize;

				$this->specialcontent = serialize($objattr);

				if ($this->tableLevel) { // *TABLES*
					$this->cell[$this->row][$this->col]['s'] += $objattr['width']; // *TABLES*
				} // *TABLES*
				// Clear properties - tidy up
				$properties = array();
				break;



			// *********** FORM - INPUT ********************

			


			// *********** GRAPH  ********************
			case 'JPGRAPH':
				if (!$this->useGraphs) {
					break;
				}
				if ($attr['TABLE']) {
					$gid = strtoupper($attr['TABLE']);
				} else {
					$gid = '0';
				}
				if (!is_array($this->graphs[$gid]) || count($this->graphs[$gid]) == 0) {
					break;
				}
				$this->ignorefollowingspaces = false;
				include_once(_MPDF_PATH . 'graph.php');
				$this->graphs[$gid]['attr'] = $attr;


				if (isset($this->graphs[$gid]['attr']['WIDTH']) && $this->graphs[$gid]['attr']['WIDTH']) {
					$this->graphs[$gid]['attr']['cWIDTH'] = $this->ConvertSize($this->graphs[$gid]['attr']['WIDTH'], $this->blk[$this->blklvl]['inner_width']);
				} // mm
				if (isset($this->graphs[$gid]['attr']['HEIGHT']) && $this->graphs[$gid]['attr']['HEIGHT']) {
					$this->graphs[$gid]['attr']['cHEIGHT'] = $this->ConvertSize($this->graphs[$gid]['attr']['HEIGHT'], $this->blk[$this->blklvl]['inner_width']);
				}

				$graph_img = print_graph($this->graphs[$gid], $this->blk[$this->blklvl]['inner_width']);
				if ($graph_img) {
					if (isset($attr['ROTATE'])) {
						if ($attr['ROTATE'] == 90 || $attr['ROTATE'] == -90) {
							$tmpw = $graph_img['w'];
							$graph_img['w'] = $graph_img['h'];
							$graph_img['h'] = $tmpw;
						}
					}
					$attr['SRC'] = $graph_img['file'];
					$attr['WIDTH'] = $graph_img['w'];
					$attr['HEIGHT'] = $graph_img['h'];
				} else {
					break;
				}

			// *********** IMAGE  ********************
			/* -- IMAGES-CORE -- */
			case 'IMG':
				$this->ignorefollowingspaces = false;
				if ($this->progressBar) {
					$this->UpdateProgressBar(1, '', 'IMG');
				} // *PROGRESS-BAR*
				$objattr = array();
				$objattr['margin_top'] = 0;
				$objattr['margin_bottom'] = 0;
				$objattr['margin_left'] = 0;
				$objattr['margin_right'] = 0;
				$objattr['padding_top'] = 0;
				$objattr['padding_bottom'] = 0;
				$objattr['padding_left'] = 0;
				$objattr['padding_right'] = 0;
				$objattr['width'] = 0;
				$objattr['height'] = 0;
				$objattr['border_top']['w'] = 0;
				$objattr['border_bottom']['w'] = 0;
				$objattr['border_left']['w'] = 0;
				$objattr['border_right']['w'] = 0;
				if (isset($attr['SRC'])) {
					$srcpath = $attr['SRC'];
					$orig_srcpath = (isset($attr['ORIG_SRC']) ? $attr['ORIG_SRC'] : '');
					$properties = $this->cssmgr->MergeCSS('', $tag, $attr);
					if (isset($properties ['DISPLAY']) && strtolower($properties ['DISPLAY']) == 'none') {
						return;
					}
					if (isset($properties['Z-INDEX']) && $this->current_layer == 0) {
						$v = intval($properties['Z-INDEX']);
						if ($v > 0) {
							$objattr['z-index'] = $v;
						}
					}

					$objattr['visibility'] = 'visible';
					if (isset($properties['VISIBILITY'])) {
						$v = strtolower($properties['VISIBILITY']);
						if (($v == 'hidden' || $v == 'printonly' || $v == 'screenonly') && $this->visibility == 'visible') {
							$objattr['visibility'] = $v;
						}
					}

					// VSPACE and HSPACE converted to margins in MergeCSS
					if (isset($properties['MARGIN-TOP'])) {
						$objattr['margin_top'] = $this->ConvertSize($properties['MARGIN-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
					if (isset($properties['MARGIN-BOTTOM'])) {
						$objattr['margin_bottom'] = $this->ConvertSize($properties['MARGIN-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
					if (isset($properties['MARGIN-LEFT'])) {
						$objattr['margin_left'] = $this->ConvertSize($properties['MARGIN-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
					if (isset($properties['MARGIN-RIGHT'])) {
						$objattr['margin_right'] = $this->ConvertSize($properties['MARGIN-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}

					if (isset($properties['PADDING-TOP'])) {
						$objattr['padding_top'] = $this->ConvertSize($properties['PADDING-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
					if (isset($properties['PADDING-BOTTOM'])) {
						$objattr['padding_bottom'] = $this->ConvertSize($properties['PADDING-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
					if (isset($properties['PADDING-LEFT'])) {
						$objattr['padding_left'] = $this->ConvertSize($properties['PADDING-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
					if (isset($properties['PADDING-RIGHT'])) {
						$objattr['padding_right'] = $this->ConvertSize($properties['PADDING-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}

					if (isset($properties['BORDER-TOP'])) {
						$objattr['border_top'] = $this->border_details($properties['BORDER-TOP']);
					}
					if (isset($properties['BORDER-BOTTOM'])) {
						$objattr['border_bottom'] = $this->border_details($properties['BORDER-BOTTOM']);
					}
					if (isset($properties['BORDER-LEFT'])) {
						$objattr['border_left'] = $this->border_details($properties['BORDER-LEFT']);
					}
					if (isset($properties['BORDER-RIGHT'])) {
						$objattr['border_right'] = $this->border_details($properties['BORDER-RIGHT']);
					}

					if (isset($properties['VERTICAL-ALIGN'])) {
						$objattr['vertical-align'] = $align[strtolower($properties['VERTICAL-ALIGN'])];
					}
					$w = 0;
					$h = 0;
					if (isset($properties['WIDTH']))
						$w = $this->ConvertSize($properties['WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					else if (isset($attr['WIDTH']))
						$w = $this->ConvertSize($attr['WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					if (isset($properties['HEIGHT']))
						$h = $this->ConvertSize($properties['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					else if (isset($attr['HEIGHT']))
						$h = $this->ConvertSize($attr['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					$maxw = $maxh = $minw = $minh = false;
					if (isset($properties['MAX-WIDTH']))
						$maxw = $this->ConvertSize($properties['MAX-WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					else if (isset($attr['MAX-WIDTH']))
						$maxw = $this->ConvertSize($attr['MAX-WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					if (isset($properties['MAX-HEIGHT']))
						$maxh = $this->ConvertSize($properties['MAX-HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					else if (isset($attr['MAX-HEIGHT']))
						$maxh = $this->ConvertSize($attr['MAX-HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					if (isset($properties['MIN-WIDTH']))
						$minw = $this->ConvertSize($properties['MIN-WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					else if (isset($attr['MIN-WIDTH']))
						$minw = $this->ConvertSize($attr['MIN-WIDTH'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					if (isset($properties['MIN-HEIGHT']))
						$minh = $this->ConvertSize($properties['MIN-HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					else if (isset($attr['MIN-HEIGHT']))
						$minh = $this->ConvertSize($attr['MIN-HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);

					if (isset($properties['OPACITY']) && $properties['OPACITY'] > 0 && $properties['OPACITY'] <= 1) {
						$objattr['opacity'] = $properties['OPACITY'];
					}
					if ($this->HREF) {
						if (strpos($this->HREF, ".") === false && strpos($this->HREF, "@") !== 0) {
							$href = $this->HREF;
							while (array_key_exists($href, $this->internallink))
								$href = "#" . $href;
							$this->internallink[$href] = $this->AddLink();
							$objattr['link'] = $this->internallink[$href];
						} else {
							$objattr['link'] = $this->HREF;
						}
					}
					$extraheight = $objattr['padding_top'] + $objattr['padding_bottom'] + $objattr['margin_top'] + $objattr['margin_bottom'] + $objattr['border_top']['w'] + $objattr['border_bottom']['w'];
					$extrawidth = $objattr['padding_left'] + $objattr['padding_right'] + $objattr['margin_left'] + $objattr['margin_right'] + $objattr['border_left']['w'] + $objattr['border_right']['w'];

					// mPDF 5.7.3 TRANSFORMS
					if (isset($properties['BACKGROUND-COLOR']) && $properties['BACKGROUND-COLOR'] != '') {
						$objattr['bgcolor'] = $this->ConvertColor($properties['BACKGROUND-COLOR']);
					}

					/* -- BACKGROUNDS -- */
					if (isset($properties['GRADIENT-MASK']) && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $properties['GRADIENT-MASK'])) {
						$objattr['GRADIENT-MASK'] = $properties['GRADIENT-MASK'];
					}
					/* -- END BACKGROUNDS -- */

					// mPDF 6
					$interpolation = false;
					if (isset($properties['IMAGE-RENDERING']) && $properties['IMAGE-RENDERING']) {
						if (strtolower($properties['IMAGE-RENDERING']) == 'crisp-edges') {
							$interpolation = false;
						} else if (strtolower($properties['IMAGE-RENDERING']) == 'optimizequality') {
							$interpolation = true;
						} else if (strtolower($properties['IMAGE-RENDERING']) == 'smooth') {
							$interpolation = true;
						} else if (strtolower($properties['IMAGE-RENDERING']) == 'auto') {
							$interpolation = $this->interpolateImages;
						} else {
							$interpolation = false;
						}
						$info['interpolation'] = $interpolation;
					}

					// Image file
					$info = $this->_getImage($srcpath, true, true, $orig_srcpath, $interpolation); // mPDF 6
					if (!$info) {
						$info = $this->_getImage($this->noImageFile);
						if ($info) {
							$srcpath = $this->noImageFile;
							$w = ($info['w'] * (25.4 / $this->dpi));
							$h = ($info['h'] * (25.4 / $this->dpi));
						}
					}
					if (!$info)
						break;

					if (isset($attr['ROTATE'])) {
						$image_orientation = $attr['ROTATE'];
					} else if (isset($properties['IMAGE-ORIENTATION'])) {
						$image_orientation = $properties['IMAGE-ORIENTATION'];
					} else {
						$image_orientation = 0;
					}
					if ($image_orientation) {
						if ($image_orientation == 90 || $image_orientation == -90 || $image_orientation == 270) {
							$tmpw = $info['w'];
							$info['w'] = $info['h'];
							$info['h'] = $tmpw;
						}
						$objattr['ROTATE'] = $image_orientation;
					}

					$objattr['file'] = $srcpath;
					//Default width and height calculation if needed
					if ($w == 0 and $h == 0) {
						/* -- IMAGES-WMF -- */
						if ($info['type'] == 'wmf') {
							// WMF units are twips (1/20pt)
							// divide by 20 to get points
							// divide by k to get user units
							$w = abs($info['w']) / (20 * _MPDFK);
							$h = abs($info['h']) / (20 * _MPDFK);
						} else
						/* -- END IMAGES-WMF -- */
						if ($info['type'] == 'svg') {
							// SVG units are pixels
							$w = abs($info['w']) / _MPDFK;
							$h = abs($info['h']) / _MPDFK;
						} else {
							//Put image at default image dpi
							$w = ($info['w'] / _MPDFK) * (72 / $this->img_dpi);
							$h = ($info['h'] / _MPDFK) * (72 / $this->img_dpi);
						}
						if (isset($properties['IMAGE-RESOLUTION'])) {
							if (preg_match('/from-image/i', $properties['IMAGE-RESOLUTION']) && isset($info['set-dpi']) && $info['set-dpi'] > 0) {
								$w *= $this->img_dpi / $info['set-dpi'];
								$h *= $this->img_dpi / $info['set-dpi'];
							} else if (preg_match('/(\d+)dpi/i', $properties['IMAGE-RESOLUTION'], $m)) {
								$dpi = $m[1];
								if ($dpi > 0) {
									$w *= $this->img_dpi / $dpi;
									$h *= $this->img_dpi / $dpi;
								}
							}
						}
					}
					// IF WIDTH OR HEIGHT SPECIFIED
					if ($w == 0)
						$w = abs($h * $info['w'] / $info['h']);
					if ($h == 0)
						$h = abs($w * $info['h'] / $info['w']);

					if ($minw && $w < $minw) {
						$w = $minw;
						$h = abs($w * $info['h'] / $info['w']);
					}
					if ($maxw && $w > $maxw) {
						$w = $maxw;
						$h = abs($w * $info['h'] / $info['w']);
					}
					if ($minh && $h < $minh) {
						$h = $minh;
						$w = abs($h * $info['w'] / $info['h']);
					}
					if ($maxh && $h > $maxh) {
						$h = $maxh;
						$w = abs($h * $info['w'] / $info['h']);
					}

					// Resize to maximum dimensions of page
					$maxWidth = $this->blk[$this->blklvl]['inner_width'];
					$maxHeight = $this->h - ($this->tMargin + $this->bMargin + 1);
					if ($this->fullImageHeight) {
						$maxHeight = $this->fullImageHeight;
					}
					if (($w + $extrawidth) > ($maxWidth + 0.0001)) { // mPDF 5.7.4  0.0001 to allow for rounding errors when w==maxWidth
						$w = $maxWidth - $extrawidth;
						$h = abs($w * $info['h'] / $info['w']);
					}

					if ($h + $extraheight > $maxHeight) {
						$h = $maxHeight - $extraheight;
						$w = abs($h * $info['w'] / $info['h']);
					}
					$objattr['type'] = 'image';
					$objattr['itype'] = $info['type'];

					$objattr['orig_h'] = $info['h'];
					$objattr['orig_w'] = $info['w'];
					/* -- IMAGES-WMF -- */
					if ($info['type'] == 'wmf') {
						$objattr['wmf_x'] = $info['x'];
						$objattr['wmf_y'] = $info['y'];
					} else
					/* -- END IMAGES-WMF -- */
					if ($info['type'] == 'svg') {
						$objattr['wmf_x'] = $info['x'];
						$objattr['wmf_y'] = $info['y'];
					}
					$objattr['height'] = $h + $extraheight;
					$objattr['width'] = $w + $extrawidth;
					$objattr['image_height'] = $h;
					$objattr['image_width'] = $w;
					/* -- CSS-IMAGE-FLOAT -- */
					if (!$this->ColActive && !$this->tableLevel && !$this->listlvl && !$this->kwt) {
						if (isset($properties['FLOAT']) && (strtoupper($properties['FLOAT']) == 'RIGHT' || strtoupper($properties['FLOAT']) == 'LEFT')) {
							$objattr['float'] = substr(strtoupper($properties['FLOAT']), 0, 1);
						}
					}
					/* -- END CSS-IMAGE-FLOAT -- */
					// mPDF 5.7.3 TRANSFORMS
					if (isset($properties['TRANSFORM']) && !$this->ColActive && !$this->kwt) {
						$objattr['transform'] = $properties['TRANSFORM'];
					}

					$e = "\xbb\xa4\xactype=image,objattr=" . serialize($objattr) . "\xbb\xa4\xac";

					// Clear properties - tidy up
					$properties = array();

					/* -- TABLES -- */
					// Output it to buffers
					if ($this->tableLevel) {
						$this->_saveCellTextBuffer($e, $this->HREF);
						$this->cell[$this->row][$this->col]['s'] += $objattr['width'];
					} else {
						/* -- END TABLES -- */
						$this->_saveTextBuffer($e, $this->HREF);
					} // *TABLES*
					/* -- ANNOTATIONS -- */
					if ($this->title2annots && isset($attr['TITLE'])) {
						$objattr = array();
						$objattr['margin_top'] = 0;
						$objattr['margin_bottom'] = 0;
						$objattr['margin_left'] = 0;
						$objattr['margin_right'] = 0;
						$objattr['width'] = 0;
						$objattr['height'] = 0;
						$objattr['border_top']['w'] = 0;
						$objattr['border_bottom']['w'] = 0;
						$objattr['border_left']['w'] = 0;
						$objattr['border_right']['w'] = 0;
						$objattr['CONTENT'] = $attr['TITLE'];
						$objattr['type'] = 'annot';
						$objattr['POS-X'] = 0;
						$objattr['POS-Y'] = 0;
						$objattr['ICON'] = 'Comment';
						$objattr['AUTHOR'] = '';
						$objattr['SUBJECT'] = '';
						$objattr['OPACITY'] = $this->annotOpacity;
						$objattr['COLOR'] = $this->ConvertColor('yellow');
						$e = "\xbb\xa4\xactype=annot,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
						if ($this->tableLevel) { // *TABLES*
							$this->cell[$this->row][$this->col]['textbuffer'][] = array($e); // *TABLES*
						} // *TABLES*
						else { // *TABLES*
							$this->textbuffer[] = array($e);
						} // *TABLES*
					}
					/* -- END ANNOTATIONS -- */
				}
				break;
			/* -- END IMAGES-CORE -- */


			// *********** CIRCULAR TEXT = TEXTCIRCLE  ********************
			case 'TEXTCIRCLE':
				$objattr = array();
				$objattr['margin_top'] = 0;
				$objattr['margin_bottom'] = 0;
				$objattr['margin_left'] = 0;
				$objattr['margin_right'] = 0;
				$objattr['padding_top'] = 0;
				$objattr['padding_bottom'] = 0;
				$objattr['padding_left'] = 0;
				$objattr['padding_right'] = 0;
				$objattr['width'] = 0;
				$objattr['height'] = 0;
				$objattr['border_top']['w'] = 0;
				$objattr['border_bottom']['w'] = 0;
				$objattr['border_left']['w'] = 0;
				$objattr['border_right']['w'] = 0;
				$objattr['top-text'] = '';
				$objattr['bottom-text'] = '';
				$objattr['r'] = 20; // radius (default value here for safety)
				$objattr['space-width'] = 120;
				$objattr['char-width'] = 100;

				$this->InlineProperties[$tag] = $this->saveInlineProperties();
				$properties = $this->cssmgr->MergeCSS('INLINE', $tag, $attr);

				if (isset($properties ['DISPLAY']) && strtolower($properties ['DISPLAY']) == 'none') {
					return;
				}
				if (isset($attr['R'])) {
					$objattr['r'] = $this->ConvertSize($attr['R'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($attr['TOP-TEXT'])) {
					$objattr['top-text'] = strcode2utf($attr['TOP-TEXT']);
					$objattr['top-text'] = $this->lesser_entity_decode($objattr['top-text']);
					if ($this->onlyCoreFonts)
						$objattr['top-text'] = mb_convert_encoding($objattr['top-text'], $this->mb_enc, 'UTF-8');
				}
				if (isset($attr['BOTTOM-TEXT'])) {
					$objattr['bottom-text'] = strcode2utf($attr['BOTTOM-TEXT']);
					$objattr['bottom-text'] = $this->lesser_entity_decode($objattr['bottom-text']);
					if ($this->onlyCoreFonts)
						$objattr['bottom-text'] = mb_convert_encoding($objattr['bottom-text'], $this->mb_enc, 'UTF-8');
				}
				if (isset($attr['SPACE-WIDTH']) && $attr['SPACE-WIDTH']) {
					$objattr['space-width'] = $attr['SPACE-WIDTH'];
				}
				if (isset($attr['CHAR-WIDTH']) && $attr['CHAR-WIDTH']) {
					$objattr['char-width'] = $attr['CHAR-WIDTH'];
				}

				// VISIBILITY
				$objattr['visibility'] = 'visible';
				if (isset($properties['VISIBILITY'])) {
					$v = strtolower($properties['VISIBILITY']);
					if (($v == 'hidden' || $v == 'printonly' || $v == 'screenonly') && $this->visibility == 'visible') {
						$objattr['visibility'] = $v;
					}
				}
				if (isset($properties['FONT-SIZE'])) {
					if (strtolower($properties['FONT-SIZE']) == 'auto') {
						if ($objattr['top-text'] && $objattr['bottom-text']) {
							$objattr['fontsize'] = -2;
						} else {
							$objattr['fontsize'] = -1;
						}
					} else {
						$mmsize = $this->ConvertSize($properties['FONT-SIZE'], ($this->default_font_size / _MPDFK));
						$this->SetFontSize($mmsize * _MPDFK, false);
						$objattr['fontsize'] = $this->FontSizePt;
					}
				}
				if (isset($attr['DIVIDER'])) {
					$objattr['divider'] = strcode2utf($attr['DIVIDER']);
					$objattr['divider'] = $this->lesser_entity_decode($objattr['divider']);
					if ($this->onlyCoreFonts)
						$objattr['divider'] = mb_convert_encoding($objattr['divider'], $this->mb_enc, 'UTF-8');
				}

				if (isset($properties['COLOR'])) {
					$objattr['color'] = $this->ConvertColor($properties['COLOR']);
				}

				$objattr['fontstyle'] = '';
				if (isset($properties['FONT-WEIGHT'])) {
					if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD') {
						$objattr['fontstyle'] .= 'B';
					}
				}
				if (isset($properties['FONT-STYLE'])) {
					if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') {
						$objattr['fontstyle'] .= 'I';
					}
				}

				if (isset($properties['FONT-FAMILY'])) {
					$this->SetFont($properties['FONT-FAMILY'], $this->FontStyle, 0, false);
				}
				$objattr['fontfamily'] = $this->FontFamily;

				// VSPACE and HSPACE converted to margins in MergeCSS
				if (isset($properties['MARGIN-TOP'])) {
					$objattr['margin_top'] = $this->ConvertSize($properties['MARGIN-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-BOTTOM'])) {
					$objattr['margin_bottom'] = $this->ConvertSize($properties['MARGIN-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-LEFT'])) {
					$objattr['margin_left'] = $this->ConvertSize($properties['MARGIN-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-RIGHT'])) {
					$objattr['margin_right'] = $this->ConvertSize($properties['MARGIN-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['PADDING-TOP'])) {
					$objattr['padding_top'] = $this->ConvertSize($properties['PADDING-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-BOTTOM'])) {
					$objattr['padding_bottom'] = $this->ConvertSize($properties['PADDING-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-LEFT'])) {
					$objattr['padding_left'] = $this->ConvertSize($properties['PADDING-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-RIGHT'])) {
					$objattr['padding_right'] = $this->ConvertSize($properties['PADDING-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['BORDER-TOP'])) {
					$objattr['border_top'] = $this->border_details($properties['BORDER-TOP']);
				}
				if (isset($properties['BORDER-BOTTOM'])) {
					$objattr['border_bottom'] = $this->border_details($properties['BORDER-BOTTOM']);
				}
				if (isset($properties['BORDER-LEFT'])) {
					$objattr['border_left'] = $this->border_details($properties['BORDER-LEFT']);
				}
				if (isset($properties['BORDER-RIGHT'])) {
					$objattr['border_right'] = $this->border_details($properties['BORDER-RIGHT']);
				}

				if (isset($properties['OPACITY']) && $properties['OPACITY'] > 0 && $properties['OPACITY'] <= 1) {
					$objattr['opacity'] = $properties['OPACITY'];
				}
				if (isset($properties['BACKGROUND-COLOR']) && $properties['BACKGROUND-COLOR'] != '') {
					$objattr['bgcolor'] = $this->ConvertColor($properties['BACKGROUND-COLOR']);
				} else {
					$objattr['bgcolor'] = false;
				}
				if ($this->HREF) {
					if (strpos($this->HREF, ".") === false && strpos($this->HREF, "@") !== 0) {
						$href = $this->HREF;
						while (array_key_exists($href, $this->internallink))
							$href = "#" . $href;
						$this->internallink[$href] = $this->AddLink();
						$objattr['link'] = $this->internallink[$href];
					} else {
						$objattr['link'] = $this->HREF;
					}
				}
				$extraheight = $objattr['padding_top'] + $objattr['padding_bottom'] + $objattr['margin_top'] + $objattr['margin_bottom'] + $objattr['border_top']['w'] + $objattr['border_bottom']['w'];
				$extrawidth = $objattr['padding_left'] + $objattr['padding_right'] + $objattr['margin_left'] + $objattr['margin_right'] + $objattr['border_left']['w'] + $objattr['border_right']['w'];


				$w = $objattr['r'] * 2;
				$h = $w;
				$objattr['height'] = $h + $extraheight;
				$objattr['width'] = $w + $extrawidth;
				$objattr['type'] = 'textcircle';

				$e = "\xbb\xa4\xactype=image,objattr=" . serialize($objattr) . "\xbb\xa4\xac";

				// Clear properties - tidy up
				$properties = array();

				/* -- TABLES -- */
				// Output it to buffers
				if ($this->tableLevel) {
					$this->_saveCellTextBuffer($e, $this->HREF);
					$this->cell[$this->row][$this->col]['s'] += $objattr['width'];
				} else {
					/* -- END TABLES -- */
					$this->_saveTextBuffer($e, $this->HREF);
				} // *TABLES*

				if ($this->InlineProperties[$tag]) {
					$this->restoreInlineProperties($this->InlineProperties[$tag]);
				}
				unset($this->InlineProperties[$tag]);

				break;


			/* -- TABLES -- */

			case 'TABLE': // TABLE-BEGIN
				$this->tdbegin = false;
				$this->lastoptionaltag = '';
				// Disable vertical justification in columns
				if ($this->ColActive) {
					$this->colvAlign = '';
				} // *COLUMNS*
				if ($this->lastblocklevelchange == 1) {
					$blockstate = 1;
				} // Top margins/padding only
				else if ($this->lastblocklevelchange < 1) {
					$blockstate = 0;
				} // NO margins/padding
				// called from block after new div e.g. <div> ... <table> ...    Outputs block top margin/border and padding
				if (count($this->textbuffer) == 0 && $this->lastblocklevelchange == 1 && !$this->tableLevel && !$this->kwt) {
					$this->newFlowingBlock($this->blk[$this->blklvl]['width'], $this->lineheight, '', false, 1, true, $this->blk[$this->blklvl]['direction']);
					$this->finishFlowingBlock(true); // true = END of flowing block
				} else if (!$this->tableLevel && count($this->textbuffer)) {
					$this->printbuffer($this->textbuffer, $blockstate);
				}

				$this->textbuffer = array();
				$this->lastblocklevelchange = -1;



				if ($this->tableLevel) { // i.e. now a nested table coming...
					// Save current level table
					$this->cell['PARENTCELL'] = $this->saveInlineProperties();
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['baseProperties'] = $this->base_table_properties;
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] = $this->cell;
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currrow'] = $this->row;
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currcol'] = $this->col;
				}
				$this->tableLevel++;
				$this->cssmgr->tbCSSlvl++;

				if ($this->tableLevel > 1) { // inherit table properties from cell in which nested
					//$this->base_table_properties['FONT-KERNING'] = ($this->textvar & FC_KERNING);	// mPDF 6
					$this->base_table_properties['LETTER-SPACING'] = $this->lSpacingCSS;
					$this->base_table_properties['WORD-SPACING'] = $this->wSpacingCSS;
					// mPDF 6
					$direction = $this->cell[$this->row][$this->col]['direction'];
					$txta = $this->cell[$this->row][$this->col]['a'];
					$cellLineHeight = $this->cell[$this->row][$this->col]['cellLineHeight'];
					$cellLineStackingStrategy = $this->cell[$this->row][$this->col]['cellLineStackingStrategy'];
					$cellLineStackingShift = $this->cell[$this->row][$this->col]['cellLineStackingShift'];
				}

				if (isset($this->tbctr[$this->tableLevel])) {
					$this->tbctr[$this->tableLevel] ++;
				} else {
					$this->tbctr[$this->tableLevel] = 1;
				}

				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['level'] = $this->tableLevel;
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['levelid'] = $this->tbctr[$this->tableLevel];

				if ($this->tableLevel > $this->innermostTableLevel) {
					$this->innermostTableLevel = $this->tableLevel;
				}
				if ($this->tableLevel > 1) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nestedpos'] = array($this->row, $this->col, $this->tbctr[($this->tableLevel - 1)]);
				}
				//++++++++++++++++++++++++++++

				$this->cell = array();
				$this->col = -1; //int
				$this->row = -1; //int
				$table = &$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]];

				// New table - any level
				$table['direction'] = $this->directionality;
				$table['bgcolor'] = false;
				$table['va'] = false;
				$table['txta'] = false;
				$table['topntail'] = false;
				$table['thead-underline'] = false;
				$table['border'] = false;
				$table['border_details']['R']['w'] = 0;
				$table['border_details']['L']['w'] = 0;
				$table['border_details']['T']['w'] = 0;
				$table['border_details']['B']['w'] = 0;
				$table['border_details']['R']['style'] = '';
				$table['border_details']['L']['style'] = '';
				$table['border_details']['T']['style'] = '';
				$table['border_details']['B']['style'] = '';
				$table['max_cell_border_width']['R'] = 0;
				$table['max_cell_border_width']['L'] = 0;
				$table['max_cell_border_width']['T'] = 0;
				$table['max_cell_border_width']['B'] = 0;
				$table['padding']['L'] = false;
				$table['padding']['R'] = false;
				$table['padding']['T'] = false;
				$table['padding']['B'] = false;
				$table['margin']['L'] = false;
				$table['margin']['R'] = false;
				$table['margin']['T'] = false;
				$table['margin']['B'] = false;
				$table['a'] = false;
				$table['border_spacing_H'] = false;
				$table['border_spacing_V'] = false;
				$table['decimal_align'] = false;
				$this->Reset();
				$this->InlineProperties = array();
				$this->InlineBDF = array(); // mPDF 6
				$this->InlineBDFctr = 0; // mPDF 6
				$table['nc'] = $table['nr'] = 0;
				$this->tablethead = 0;
				$this->tabletfoot = 0;
				$this->tabletheadjustfinished = false;

				// mPDF 6
				if ($this->tableLevel > 1) { // inherit table properties from cell in which nested
					$table['direction'] = $direction;
					$table['txta'] = $txta;
					$table['cellLineHeight'] = $cellLineHeight;
					$table['cellLineStackingStrategy'] = $cellLineStackingStrategy;
					$table['cellLineStackingShift'] = $cellLineStackingShift;
				}


				if ($this->blockjustfinished && !count($this->textbuffer) && $this->y != $this->tMargin && $this->collapseBlockMargins && $this->tableLevel == 1) {
					$lastbottommargin = $this->lastblockbottommargin;
				} else {
					$lastbottommargin = 0;
				}
				$this->lastblockbottommargin = 0;
				$this->blockjustfinished = false;

				if ($this->tableLevel == 1) {
					$table['headernrows'] = 0;
					$table['footernrows'] = 0;
					$this->base_table_properties = array();
				}

				// ADDED CSS FUNCIONS FOR TABLE
				if ($this->cssmgr->tbCSSlvl == 1) {
					$properties = $this->cssmgr->MergeCSS('TOPTABLE', $tag, $attr);
				} else {
					$properties = $this->cssmgr->MergeCSS('TABLE', $tag, $attr);
				}

				$w = '';
				if (isset($properties['WIDTH'])) {
					$w = $properties['WIDTH'];
				} else if (isset($attr['WIDTH']) && $attr['WIDTH']) {
					$w = $attr['WIDTH'];
				}

				if (isset($attr['ALIGN']) && isset($align[strtolower($attr['ALIGN'])])) {
					$table['a'] = $align[strtolower($attr['ALIGN'])];
				}
				if (!$table['a']) {
					if ($table['direction'] == 'rtl') {
						$table['a'] = 'R';
					} else {
						$table['a'] = 'L';
					}
				}

				if (isset($properties['DIRECTION']) && $properties['DIRECTION']) {
					$table['direction'] = strtolower($properties['DIRECTION']);
				} else if (isset($attr['DIR']) && $attr['DIR']) {
					$table['direction'] = strtolower($attr['DIR']);
				} else if ($this->tableLevel == 1) {
					$table['direction'] = $this->blk[$this->blklvl]['direction'];
				}

				if (isset($properties['BACKGROUND-COLOR'])) {
					$table['bgcolor'][-1] = $properties['BACKGROUND-COLOR'];
				} else if (isset($properties['BACKGROUND'])) {
					$table['bgcolor'][-1] = $properties['BACKGROUND'];
				} else if (isset($attr['BGCOLOR'])) {
					$table['bgcolor'][-1] = $attr['BGCOLOR'];
				}

				if (isset($properties['VERTICAL-ALIGN']) && isset($align[strtolower($properties['VERTICAL-ALIGN'])])) {
					$table['va'] = $align[strtolower($properties['VERTICAL-ALIGN'])];
				}
				if (isset($properties['TEXT-ALIGN']) && isset($align[strtolower($properties['TEXT-ALIGN'])])) {
					$table['txta'] = $align[strtolower($properties['TEXT-ALIGN'])];
				}

				if (isset($properties['AUTOSIZE']) && $properties['AUTOSIZE'] && $this->tableLevel == 1) {
					$this->shrink_this_table_to_fit = $properties['AUTOSIZE'];
					if ($this->shrink_this_table_to_fit < 1) {
						$this->shrink_this_table_to_fit = 0;
					}
				}
				if (isset($properties['ROTATE']) && $properties['ROTATE'] && $this->tableLevel == 1) {
					$this->table_rotate = $properties['ROTATE'];
				}
				if (isset($properties['TOPNTAIL'])) {
					$table['topntail'] = $properties['TOPNTAIL'];
				}
				if (isset($properties['THEAD-UNDERLINE'])) {
					$table['thead-underline'] = $properties['THEAD-UNDERLINE'];
				}

				if (isset($properties['BORDER'])) {
					$bord = $this->border_details($properties['BORDER']);
					if ($bord['s']) {
						$table['border'] = _BORDER_ALL;
						$table['border_details']['R'] = $bord;
						$table['border_details']['L'] = $bord;
						$table['border_details']['T'] = $bord;
						$table['border_details']['B'] = $bord;
					}
				}
				if (isset($properties['BORDER-RIGHT'])) {
					if ($table['direction'] == 'rtl') {  // *OTL*
						$table['border_details']['R'] = $this->border_details($properties['BORDER-LEFT']); // *OTL*
					} // *OTL*
					else { // *OTL*
						$table['border_details']['R'] = $this->border_details($properties['BORDER-RIGHT']);
					} // *OTL*
					$this->setBorder($table['border'], _BORDER_RIGHT, $table['border_details']['R']['s']);
				}
				if (isset($properties['BORDER-LEFT'])) {
					if ($table['direction'] == 'rtl') {  // *OTL*
						$table['border_details']['L'] = $this->border_details($properties['BORDER-RIGHT']); // *OTL*
					} // *OTL*
					else { // *OTL*
						$table['border_details']['L'] = $this->border_details($properties['BORDER-LEFT']);
					} // *OTL*
					$this->setBorder($table['border'], _BORDER_LEFT, $table['border_details']['L']['s']);
				}
				if (isset($properties['BORDER-BOTTOM'])) {
					$table['border_details']['B'] = $this->border_details($properties['BORDER-BOTTOM']);
					$this->setBorder($table['border'], _BORDER_BOTTOM, $table['border_details']['B']['s']);
				}
				if (isset($properties['BORDER-TOP'])) {
					$table['border_details']['T'] = $this->border_details($properties['BORDER-TOP']);
					$this->setBorder($table['border'], _BORDER_TOP, $table['border_details']['T']['s']);
				}
				if ($table['border']) {
					$this->table_border_css_set = 1;
				} else {
					$this->table_border_css_set = 0;
				}

				// mPDF 6
				if (isset($properties['LANG']) && $properties['LANG']) {
					if ($this->autoLangToFont && !$this->usingCoreFont) {
						if ($properties['LANG'] != $this->default_lang && $properties['LANG'] != 'UTF-8') {
							list ($coreSuitable, $mpdf_pdf_unifont) = GetLangOpts($properties['LANG'], $this->useAdobeCJK, $this->fontdata);
							if ($mpdf_pdf_unifont) {
								$properties['FONT-FAMILY'] = $mpdf_pdf_unifont;
							}
						}
					}
					$this->currentLang = $properties['LANG'];
				}


				if (isset($properties['FONT-FAMILY'])) {
					$this->default_font = $properties['FONT-FAMILY'];
					$this->SetFont($this->default_font, '', 0, false);
				}
				$this->base_table_properties['FONT-FAMILY'] = $this->FontFamily;

				if (isset($properties['FONT-SIZE'])) {
					if ($this->tableLevel > 1) {
						$mmsize = $this->ConvertSize($properties['FONT-SIZE'], $this->base_table_properties['FONT-SIZE']);
					} else {
						$mmsize = $this->ConvertSize($properties['FONT-SIZE'], $this->default_font_size / _MPDFK);
					}
					if ($mmsize) {
						$this->default_font_size = $mmsize * (_MPDFK);
						$this->SetFontSize($this->default_font_size, false);
					}
				}
				$this->base_table_properties['FONT-SIZE'] = $this->FontSize . 'mm';

				if (isset($properties['FONT-WEIGHT'])) {
					if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD') {
						$this->base_table_properties['FONT-WEIGHT'] = 'BOLD';
					}
				}
				if (isset($properties['FONT-STYLE'])) {
					if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') {
						$this->base_table_properties['FONT-STYLE'] = 'ITALIC';
					}
				}
				if (isset($properties['COLOR'])) {
					$this->base_table_properties['COLOR'] = $properties['COLOR'];
				}
				if (isset($properties['FONT-KERNING'])) {
					$this->base_table_properties['FONT-KERNING'] = $properties['FONT-KERNING'];
				}
				if (isset($properties['LETTER-SPACING'])) {
					$this->base_table_properties['LETTER-SPACING'] = $properties['LETTER-SPACING'];
				}
				if (isset($properties['WORD-SPACING'])) {
					$this->base_table_properties['WORD-SPACING'] = $properties['WORD-SPACING'];
				}
				// mPDF 6
				if (isset($properties['HYPHENS'])) {
					$this->base_table_properties['HYPHENS'] = $properties['HYPHENS'];
				}
				if (isset($properties['LINE-HEIGHT']) && $properties['LINE-HEIGHT']) {
					$table['cellLineHeight'] = $this->fixLineheight($properties['LINE-HEIGHT']);
				} else if ($this->tableLevel == 1) {
					$table['cellLineHeight'] = $this->blk[$this->blklvl]['line_height'];
				}

				if (isset($properties['LINE-STACKING-STRATEGY']) && $properties['LINE-STACKING-STRATEGY']) {
					$table['cellLineStackingStrategy'] = strtolower($properties['LINE-STACKING-STRATEGY']);
				} else if ($this->tableLevel == 1 && isset($this->blk[$this->blklvl]['line_stacking_strategy'])) {
					$table['cellLineStackingStrategy'] = $this->blk[$this->blklvl]['line_stacking_strategy'];
				} else {
					$table['cellLineStackingStrategy'] = 'inline-line-height';
				}

				if (isset($properties['LINE-STACKING-SHIFT']) && $properties['LINE-STACKING-SHIFT']) {
					$table['cellLineStackingShift'] = strtolower($properties['LINE-STACKING-SHIFT']);
				} else if ($this->tableLevel == 1 && isset($this->blk[$this->blklvl]['line_stacking_shift'])) {
					$table['cellLineStackingShift'] = $this->blk[$this->blklvl]['line_stacking_shift'];
				} else {
					$table['cellLineStackingShift'] = 'consider-shifts';
				}

				if (isset($properties['PADDING-LEFT'])) {
					$table['padding']['L'] = $this->ConvertSize($properties['PADDING-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-RIGHT'])) {
					$table['padding']['R'] = $this->ConvertSize($properties['PADDING-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-TOP'])) {
					$table['padding']['T'] = $this->ConvertSize($properties['PADDING-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-BOTTOM'])) {
					$table['padding']['B'] = $this->ConvertSize($properties['PADDING-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['MARGIN-TOP'])) {
					if ($lastbottommargin) {
						$tmp = $this->ConvertSize($properties['MARGIN-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
						if ($tmp > $lastbottommargin) {
							$properties['MARGIN-TOP'] -= $lastbottommargin;
						} else {
							$properties['MARGIN-TOP'] = 0;
						}
					}
					$table['margin']['T'] = $this->ConvertSize($properties['MARGIN-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['MARGIN-BOTTOM'])) {
					$table['margin']['B'] = $this->ConvertSize($properties['MARGIN-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-LEFT'])) {
					$table['margin']['L'] = $this->ConvertSize($properties['MARGIN-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				if (isset($properties['MARGIN-RIGHT'])) {
					$table['margin']['R'] = $this->ConvertSize($properties['MARGIN-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['MARGIN-LEFT']) && isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-LEFT']) == 'auto' && strtolower($properties['MARGIN-RIGHT']) == 'auto') {
					$table['a'] = 'C';
				} else if (isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT']) == 'auto') {
					$table['a'] = 'R';
				} else if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT']) == 'auto') {
					$table['a'] = 'L';
				}

				if (isset($properties['BORDER-COLLAPSE']) && strtoupper($properties['BORDER-COLLAPSE']) == 'SEPARATE') {
					$table['borders_separate'] = true;
				} else {
					$table['borders_separate'] = false;
				}

				// mPDF 5.7.3

				if (isset($properties['BORDER-SPACING-H'])) {
					$table['border_spacing_H'] = $this->ConvertSize($properties['BORDER-SPACING-H'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['BORDER-SPACING-V'])) {
					$table['border_spacing_V'] = $this->ConvertSize($properties['BORDER-SPACING-V'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				// mPDF 5.7.3
				if (!$table['borders_separate']) {
					$table['border_spacing_H'] = $table['border_spacing_V'] = 0;
				}

				if (isset($properties['EMPTY-CELLS'])) {
					$table['empty_cells'] = strtolower($properties['EMPTY-CELLS']);  // 'hide'  or 'show'
				} else {
					$table['empty_cells'] = '';
				}

				if (isset($properties['PAGE-BREAK-INSIDE']) && strtoupper($properties['PAGE-BREAK-INSIDE']) == 'AVOID' && $this->tableLevel == 1 && !$this->writingHTMLfooter) {
					$this->table_keep_together = true;
				} else if ($this->tableLevel == 1) {
					$this->table_keep_together = false;
				}
				if (isset($properties['PAGE-BREAK-AFTER']) && $this->tableLevel == 1) {
					$table['page_break_after'] = strtoupper($properties['PAGE-BREAK-AFTER']);
				}

				/* -- BACKGROUNDS -- */
				if (isset($properties['BACKGROUND-GRADIENT']) && !$this->kwt && !$this->ColActive) {
					$table['gradient'] = $properties['BACKGROUND-GRADIENT'];
				}

				if (isset($properties['BACKGROUND-IMAGE']) && $properties['BACKGROUND-IMAGE'] && !$this->kwt && !$this->ColActive) {
					$ret = $this->SetBackground($properties, $currblk['inner_width']);
					if ($ret) {
						$table['background-image'] = $ret;
					}
				}
				/* -- END BACKGROUNDS -- */

				if (isset($properties['OVERFLOW'])) {
					$table['overflow'] = strtolower($properties['OVERFLOW']);  // 'hidden' 'wrap' or 'visible' or 'auto'
					if (($this->ColActive || $this->tableLevel > 1) && $table['overflow'] == 'visible') {
						unset($table['overflow']);
					}
				}

				$properties = array();


				if (isset($attr['CELLPADDING'])) {
					$table['cell_padding'] = $attr['CELLPADDING'];
				} else {
					$table['cell_padding'] = false;
				}

				if (isset($attr['BORDER']) && $attr['BORDER'] == '1') {
					$this->table_border_attr_set = 1;
					$bord = $this->border_details('#000000 1px solid');
					if ($bord['s']) {
						$table['border'] = _BORDER_ALL;
						$table['border_details']['R'] = $bord;
						$table['border_details']['L'] = $bord;
						$table['border_details']['T'] = $bord;
						$table['border_details']['B'] = $bord;
					}
				} else {
					$this->table_border_attr_set = 0;
				}

				if ($w) {
					$maxwidth = $this->blk[$this->blklvl]['inner_width'];
					if ($table['borders_separate']) {
						$tblblw = $table['margin']['L'] + $table['margin']['R'] + $table['border_details']['L']['w'] / 2 + $table['border_details']['R']['w'] / 2;
					} else {
						$tblblw = $table['margin']['L'] + $table['margin']['R'] + $table['max_cell_border_width']['L'] / 2 + $table['max_cell_border_width']['R'] / 2;
					}
					if (strpos($w, '%') && $this->tableLevel == 1 && !$this->ignore_table_percents) {
						// % needs to be of inner box without table margins etc.
						$maxwidth -= $tblblw;
						$wmm = $this->ConvertSize($w, $maxwidth, $this->FontSize, false);
						$table['w'] = $wmm + $tblblw;
					}
					if (strpos($w, '%') && $this->tableLevel > 1 && !$this->ignore_table_percents && $this->keep_table_proportions) {
						$table['wpercent'] = $w + 0;  // makes 80% -> 80
					}
					if (!strpos($w, '%') && !$this->ignore_table_widths) {
						$wmm = $this->ConvertSize($w, $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
						$table['w'] = $wmm + $tblblw;
					}
					if (!$this->keep_table_proportions) {
						if (isset($table['w']) && $table['w'] > $this->blk[$this->blklvl]['inner_width']) {
							$table['w'] = $this->blk[$this->blklvl]['inner_width'];
						}
					}
				}

				if (isset($attr['AUTOSIZE']) && $this->tableLevel == 1) {
					$this->shrink_this_table_to_fit = $attr['AUTOSIZE'];
					if ($this->shrink_this_table_to_fit < 1) {
						$this->shrink_this_table_to_fit = 1;
					}
				}
				if (isset($attr['ROTATE']) && $this->tableLevel == 1) {
					$this->table_rotate = $attr['ROTATE'];
				}

				//++++++++++++++++++++++++++++
				if ($this->table_rotate) {
					$this->tbrot_Links = array();
					$this->tbrot_Annots = array();
					$this->tbrotForms = array();
					$this->tbrot_BMoutlines = array();
					$this->tbrot_toc = array();
				}

				if ($this->kwt) {
					if ($this->table_rotate) {
						$this->table_keep_together = true;
					}
					$this->kwt = false;
					$this->kwt_saved = true;
				}

				if ($this->tableLevel == 1 && $this->useGraphs) {
					if (isset($attr['ID']) && $attr['ID']) {
						$this->currentGraphId = strtoupper($attr['ID']);
					} else {
						$this->currentGraphId = '0';
					}
					$this->graphs[$this->currentGraphId] = array();
				}
				//++++++++++++++++++++++++++++
				$this->plainCell_properties = array();
				unset($table);
				break;

			case 'THEAD':
				$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
				$this->cssmgr->tbCSSlvl++;
				$this->tablethead = 1;
				$this->tabletfoot = 0;
				$properties = $this->cssmgr->MergeCSS('TABLE', $tag, $attr);
				if (isset($properties['FONT-WEIGHT'])) {
					if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD') {
						$this->thead_font_weight = 'B';
					} else {
						$this->thead_font_weight = '';
					}
				}

				if (isset($properties['FONT-STYLE'])) {
					if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') {
						$this->thead_font_style = 'I';
					} else {
						$this->thead_font_style = '';
					}
				}
				if (isset($properties['FONT-VARIANT'])) {
					if (strtoupper($properties['FONT-VARIANT']) == 'SMALL-CAPS') {
						$this->thead_font_smCaps = 'S';
					} else {
						$this->thead_font_smCaps = '';
					}
				}

				if (isset($properties['VERTICAL-ALIGN'])) {
					$this->thead_valign_default = $properties['VERTICAL-ALIGN'];
				}
				if (isset($properties['TEXT-ALIGN'])) {
					$this->thead_textalign_default = $properties['TEXT-ALIGN'];
				}
				$properties = array();
				break;

			case 'TFOOT':
				$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
				$this->cssmgr->tbCSSlvl++;
				$this->tabletfoot = 1;
				$this->tablethead = 0;
				$properties = $this->cssmgr->MergeCSS('TABLE', $tag, $attr);
				if (isset($properties['FONT-WEIGHT'])) {
					if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD') {
						$this->tfoot_font_weight = 'B';
					} else {
						$this->tfoot_font_weight = '';
					}
				}

				if (isset($properties['FONT-STYLE'])) {
					if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') {
						$this->tfoot_font_style = 'I';
					} else {
						$this->tfoot_font_style = '';
					}
				}
				if (isset($properties['FONT-VARIANT'])) {
					if (strtoupper($properties['FONT-VARIANT']) == 'SMALL-CAPS') {
						$this->tfoot_font_smCaps = 'S';
					} else {
						$this->tfoot_font_smCaps = '';
					}
				}

				if (isset($properties['VERTICAL-ALIGN'])) {
					$this->tfoot_valign_default = $properties['VERTICAL-ALIGN'];
				}
				if (isset($properties['TEXT-ALIGN'])) {
					$this->tfoot_textalign_default = $properties['TEXT-ALIGN'];
				}
				$properties = array();
				break;


			case 'TBODY':
				$this->tablethead = 0;
				$this->tabletfoot = 0;
				$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
				$this->cssmgr->tbCSSlvl++;
				$this->cssmgr->MergeCSS('TABLE', $tag, $attr);
				break;


			case 'TR':
				$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
				$this->cssmgr->tbCSSlvl++;
				$this->row++;
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr'] ++;
				$this->col = -1;
				$properties = $this->cssmgr->MergeCSS('TABLE', $tag, $attr);

				if (!$this->simpleTables && (!isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['borders_separate']) || !$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['borders_separate'])) {
					if (isset($properties['BORDER-LEFT']) && $properties['BORDER-LEFT']) {
						$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trborder-left'][$this->row] = $properties['BORDER-LEFT'];
					}
					if (isset($properties['BORDER-RIGHT']) && $properties['BORDER-RIGHT']) {
						$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trborder-right'][$this->row] = $properties['BORDER-RIGHT'];
					}
					if (isset($properties['BORDER-TOP']) && $properties['BORDER-TOP']) {
						$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trborder-top'][$this->row] = $properties['BORDER-TOP'];
					}
					if (isset($properties['BORDER-BOTTOM']) && $properties['BORDER-BOTTOM']) {
						$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trborder-bottom'][$this->row] = $properties['BORDER-BOTTOM'];
					}
				}

				if (isset($properties['BACKGROUND-COLOR'])) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$this->row] = $properties['BACKGROUND-COLOR'];
				} else if (isset($attr['BGCOLOR']))
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$this->row] = $attr['BGCOLOR'];

				/* -- BACKGROUNDS -- */
				if (isset($properties['BACKGROUND-GRADIENT']) && !$this->kwt && !$this->ColActive) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trgradients'][$this->row] = $properties['BACKGROUND-GRADIENT'];
				}

				if (isset($properties['BACKGROUND-IMAGE']) && $properties['BACKGROUND-IMAGE'] && !$this->kwt && !$this->ColActive) {
					$ret = $this->SetBackground($properties, $currblk['inner_width']);
					if ($ret) {
						$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trbackground-images'][$this->row] = $ret;
					}
				}
				/* -- END BACKGROUNDS -- */




				if (isset($properties['TEXT-ROTATE'])) {
					$this->trow_text_rotate = $properties['TEXT-ROTATE'];
				}
				if (isset($attr['TEXT-ROTATE']))
					$this->trow_text_rotate = $attr['TEXT-ROTATE'];

				if ($this->tablethead) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_thead'][$this->row] = true;
				}
				if ($this->tabletfoot) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'][$this->row] = true;
				}
				$properties = array();
				break;


			case 'TH':
			case 'TD':
				$this->ignorefollowingspaces = true;
				$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
				$this->cssmgr->tbCSSlvl++;
				$this->InlineProperties = array();
				$this->InlineBDF = array(); // mPDF 6
				$this->InlineBDFctr = 0; // mPDF 6
				$this->tdbegin = true;
				$this->col++;
				while (isset($this->cell[$this->row][$this->col])) {
					$this->col++;
				}

				//Update number column
				if ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] < $this->col + 1) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] = $this->col + 1;
				}

				$table = &$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]];

				$c = array('a' => false,
					'R' => false,
					'nowrap' => false,
					'bgcolor' => false,
					'padding' => array('L' => false,
						'R' => false,
						'T' => false,
						'B' => false
					)
				);

				if ($this->simpleTables && $this->row == 0 && $this->col == 0) {
					$table['simple']['border'] = false;
					$table['simple']['border_details']['R']['w'] = 0;
					$table['simple']['border_details']['L']['w'] = 0;
					$table['simple']['border_details']['T']['w'] = 0;
					$table['simple']['border_details']['B']['w'] = 0;
					$table['simple']['border_details']['R']['style'] = '';
					$table['simple']['border_details']['L']['style'] = '';
					$table['simple']['border_details']['T']['style'] = '';
					$table['simple']['border_details']['B']['style'] = '';
				} else if (!$this->simpleTables) {
					$c['border'] = false;
					$c['border_details']['R']['w'] = 0;
					$c['border_details']['L']['w'] = 0;
					$c['border_details']['T']['w'] = 0;
					$c['border_details']['B']['w'] = 0;
					$c['border_details']['mbw']['BL'] = 0;
					$c['border_details']['mbw']['BR'] = 0;
					$c['border_details']['mbw']['RT'] = 0;
					$c['border_details']['mbw']['RB'] = 0;
					$c['border_details']['mbw']['TL'] = 0;
					$c['border_details']['mbw']['TR'] = 0;
					$c['border_details']['mbw']['LT'] = 0;
					$c['border_details']['mbw']['LB'] = 0;
					$c['border_details']['R']['style'] = '';
					$c['border_details']['L']['style'] = '';
					$c['border_details']['T']['style'] = '';
					$c['border_details']['B']['style'] = '';
					$c['border_details']['R']['s'] = 0;
					$c['border_details']['L']['s'] = 0;
					$c['border_details']['T']['s'] = 0;
					$c['border_details']['B']['s'] = 0;
					$c['border_details']['R']['c'] = $this->ConvertColor(0);
					$c['border_details']['L']['c'] = $this->ConvertColor(0);
					$c['border_details']['T']['c'] = $this->ConvertColor(0);
					$c['border_details']['B']['c'] = $this->ConvertColor(0);
					$c['border_details']['R']['dom'] = 0;
					$c['border_details']['L']['dom'] = 0;
					$c['border_details']['T']['dom'] = 0;
					$c['border_details']['B']['dom'] = 0;
					$c['border_details']['cellposdom'] = 0;
				}


				if ($table['va']) {
					$c['va'] = $table['va'];
				}
				if ($table['txta']) {
					$c['a'] = $table['txta'];
				}
				if ($this->table_border_attr_set) {
					if ($table['border_details']) {
						if (!$this->simpleTables) {
							$c['border_details']['R'] = $table['border_details']['R'];
							$c['border_details']['L'] = $table['border_details']['L'];
							$c['border_details']['T'] = $table['border_details']['T'];
							$c['border_details']['B'] = $table['border_details']['B'];
							$c['border'] = $table['border'];
							$c['border_details']['L']['dom'] = 1;
							$c['border_details']['R']['dom'] = 1;
							$c['border_details']['T']['dom'] = 1;
							$c['border_details']['B']['dom'] = 1;
						} else if ($this->simpleTables && $this->row == 0 && $this->col == 0) {
							$table['simple']['border_details']['R'] = $table['border_details']['R'];
							$table['simple']['border_details']['L'] = $table['border_details']['L'];
							$table['simple']['border_details']['T'] = $table['border_details']['T'];
							$table['simple']['border_details']['B'] = $table['border_details']['B'];
							$table['simple']['border'] = $table['border'];
						}
					}
				}
				// INHERITED THEAD CSS Properties
				if ($this->tablethead) {
					if ($this->thead_valign_default)
						$c['va'] = $align[strtolower($this->thead_valign_default)];
					if ($this->thead_textalign_default)
						$c['a'] = $align[strtolower($this->thead_textalign_default)];
					if ($this->thead_font_weight == 'B') {
						$this->SetStyle('B', true);
					}
					if ($this->thead_font_style == 'I') {
						$this->SetStyle('I', true);
					}
					if ($this->thead_font_smCaps == 'S') {
						$this->textvar = ($this->textvar | FC_SMALLCAPS);
					} // mPDF 5.7.1
				}

				// INHERITED TFOOT CSS Properties
				if ($this->tabletfoot) {
					if ($this->tfoot_valign_default)
						$c['va'] = $align[strtolower($this->tfoot_valign_default)];
					if ($this->tfoot_textalign_default)
						$c['a'] = $align[strtolower($this->tfoot_textalign_default)];
					if ($this->tfoot_font_weight == 'B') {
						$this->SetStyle('B', true);
					}
					if ($this->tfoot_font_style == 'I') {
						$this->SetStyle('I', true);
					}
					if ($this->tfoot_font_style == 'S') {
						$this->textvar = ($this->textvar | FC_SMALLCAPS);
					} // mPDF 5.7.1
				}


				if ($this->trow_text_rotate) {
					$c['R'] = $this->trow_text_rotate;
				}

				$this->cell_border_dominance_L = 0;
				$this->cell_border_dominance_R = 0;
				$this->cell_border_dominance_T = 0;
				$this->cell_border_dominance_B = 0;

				$properties = $this->cssmgr->MergeCSS('TABLE', $tag, $attr);

				$properties = $this->cssmgr->array_merge_recursive_unique($this->base_table_properties, $properties);

				$this->Reset(); // mPDF 6   ?????????????????????

				$this->setCSS($properties, 'TABLECELL', $tag);

				$c['dfs'] = $this->FontSize; // Default Font size


				if (isset($properties['BACKGROUND-COLOR'])) {
					$c['bgcolor'] = $properties['BACKGROUND-COLOR'];
				} else if (isset($properties['BACKGROUND'])) {
					$c['bgcolor'] = $properties['BACKGROUND'];
				} else if (isset($attr['BGCOLOR']))
					$c['bgcolor'] = $attr['BGCOLOR'];



				/* -- BACKGROUNDS -- */
				if (isset($properties['BACKGROUND-GRADIENT'])) {
					$c['gradient'] = $properties['BACKGROUND-GRADIENT'];
				} else {
					$c['gradient'] = false;
				}

				if (isset($properties['BACKGROUND-IMAGE']) && $properties['BACKGROUND-IMAGE'] && !$this->keep_block_together) {
					$ret = $this->SetBackground($properties, $this->blk[$this->blklvl]['inner_width']);
					if ($ret) {
						$c['background-image'] = $ret;
					}
				}
				/* -- END BACKGROUNDS -- */
				if (isset($properties['VERTICAL-ALIGN'])) {
					$c['va'] = $align[strtolower($properties['VERTICAL-ALIGN'])];
				} else if (isset($attr['VALIGN']))
					$c['va'] = $align[strtolower($attr['VALIGN'])];


				if (isset($properties['TEXT-ALIGN']) && $properties['TEXT-ALIGN']) {
					if (substr($properties['TEXT-ALIGN'], 0, 1) == 'D') {
						$c['a'] = $properties['TEXT-ALIGN'];
					} else {
						$c['a'] = $align[strtolower($properties['TEXT-ALIGN'])];
					}
				}
				if (isset($attr['ALIGN']) && $attr['ALIGN']) {
					if (strtolower($attr['ALIGN']) == 'char') {
						if (isset($attr['CHAR']) && $attr['CHAR']) {
							$char = html_entity_decode($attr['CHAR']);
							$char = strcode2utf($char);
							$d = array_search($char, $this->decimal_align);
							if ($d !== false) {
								$c['a'] = $d . 'R';
							}
						} else {
							$c['a'] = 'DPR';
						}
					} else {
						$c['a'] = $align[strtolower($attr['ALIGN'])];
					}
				}

				// mPDF 6
				$c['direction'] = $table['direction'];
				if (isset($attr['DIR']) and $attr['DIR'] != '') {
					$c['direction'] = strtolower($attr['DIR']);
				}
				if (isset($properties['DIRECTION'])) {
					$c['direction'] = strtolower($properties['DIRECTION']);
				}

				if (!$c['a']) {
					if (isset($c['direction']) && $c['direction'] == 'rtl') {
						$c['a'] = 'R';
					} else {
						$c['a'] = 'L';
					}
				}

				$c['cellLineHeight'] = $table['cellLineHeight'];
				if (isset($properties['LINE-HEIGHT'])) {
					$c['cellLineHeight'] = $this->fixLineheight($properties['LINE-HEIGHT']);
				}

				$c['cellLineStackingStrategy'] = $table['cellLineStackingStrategy'];
				if (isset($properties['LINE-STACKING-STRATEGY'])) {
					$c['cellLineStackingStrategy'] = strtolower($properties['LINE-STACKING-STRATEGY']);
				}

				$c['cellLineStackingShift'] = $table['cellLineStackingShift'];
				if (isset($properties['LINE-STACKING-SHIFT'])) {
					$c['cellLineStackingShift'] = strtolower($properties['LINE-STACKING-SHIFT']);
				}

				if (isset($properties['TEXT-ROTATE']) && ($properties['TEXT-ROTATE'] || $properties['TEXT-ROTATE'] === "0")) {
					$c['R'] = $properties['TEXT-ROTATE'];
				}
				if (isset($properties['BORDER'])) {
					$bord = $this->border_details($properties['BORDER']);
					if ($bord['s']) {
						if (!$this->simpleTables) {
							$c['border'] = _BORDER_ALL;
							$c['border_details']['R'] = $bord;
							$c['border_details']['L'] = $bord;
							$c['border_details']['T'] = $bord;
							$c['border_details']['B'] = $bord;
							$c['border_details']['L']['dom'] = $this->cell_border_dominance_L;
							$c['border_details']['R']['dom'] = $this->cell_border_dominance_R;
							$c['border_details']['T']['dom'] = $this->cell_border_dominance_T;
							$c['border_details']['B']['dom'] = $this->cell_border_dominance_B;
						} else if ($this->simpleTables && $this->row == 0 && $this->col == 0) {
							$table['simple']['border'] = _BORDER_ALL;
							$table['simple']['border_details']['R'] = $bord;
							$table['simple']['border_details']['L'] = $bord;
							$table['simple']['border_details']['T'] = $bord;
							$table['simple']['border_details']['B'] = $bord;
						}
					}
				}
				if (!$this->simpleTables) {
					if (isset($properties['BORDER-RIGHT']) && $properties['BORDER-RIGHT']) {
						$c['border_details']['R'] = $this->border_details($properties['BORDER-RIGHT']);
						$this->setBorder($c['border'], _BORDER_RIGHT, $c['border_details']['R']['s']);
						$c['border_details']['R']['dom'] = $this->cell_border_dominance_R;
					}
					if (isset($properties['BORDER-LEFT']) && $properties['BORDER-LEFT']) {
						$c['border_details']['L'] = $this->border_details($properties['BORDER-LEFT']);
						$this->setBorder($c['border'], _BORDER_LEFT, $c['border_details']['L']['s']);
						$c['border_details']['L']['dom'] = $this->cell_border_dominance_L;
					}
					if (isset($properties['BORDER-BOTTOM']) && $properties['BORDER-BOTTOM']) {
						$c['border_details']['B'] = $this->border_details($properties['BORDER-BOTTOM']);
						$this->setBorder($c['border'], _BORDER_BOTTOM, $c['border_details']['B']['s']);
						$c['border_details']['B']['dom'] = $this->cell_border_dominance_B;
					}
					if (isset($properties['BORDER-TOP']) && $properties['BORDER-TOP']) {
						$c['border_details']['T'] = $this->border_details($properties['BORDER-TOP']);
						$this->setBorder($c['border'], _BORDER_TOP, $c['border_details']['T']['s']);
						$c['border_details']['T']['dom'] = $this->cell_border_dominance_T;
					}
				} else if ($this->simpleTables && $this->row == 0 && $this->col == 0) {
					if (isset($properties['BORDER-LEFT']) && $properties['BORDER-LEFT']) {
						$bord = $this->border_details($properties['BORDER-LEFT']);
						if ($bord['s']) {
							$table['simple']['border'] = _BORDER_ALL;
						} else {
							$table['simple']['border'] = 0;
						}
						$table['simple']['border_details']['R'] = $bord;
						$table['simple']['border_details']['L'] = $bord;
						$table['simple']['border_details']['T'] = $bord;
						$table['simple']['border_details']['B'] = $bord;
					}
				}

				if ($this->simpleTables && $this->row == 0 && $this->col == 0 && !$table['borders_separate'] && $table['simple']['border']) {
					$table['border_details'] = $table['simple']['border_details'];
					$table['border'] = $table['simple']['border'];
				}

				// Border set on TR (if collapsed only)
				if (!$table['borders_separate'] && !$this->simpleTables && isset($table['trborder-left'][$this->row])) {
					if ($this->col == 0) {
						$left = $this->border_details($table['trborder-left'][$this->row]);
						$c['border_details']['L'] = $left;
						$this->setBorder($c['border'], _BORDER_LEFT, $c['border_details']['L']['s']);
					}
					$c['border_details']['B'] = $this->border_details($table['trborder-bottom'][$this->row]);
					$this->setBorder($c['border'], _BORDER_BOTTOM, $c['border_details']['B']['s']);
					$c['border_details']['T'] = $this->border_details($table['trborder-top'][$this->row]);
					$this->setBorder($c['border'], _BORDER_TOP, $c['border_details']['T']['s']);
				}

				if ($this->packTableData && !$this->simpleTables) {
					$c['borderbin'] = $this->_packCellBorder($c);
					unset($c['border']);
					unset($c['border_details']);
				}

				if (isset($properties['PADDING-LEFT'])) {
					$c['padding']['L'] = $this->ConvertSize($properties['PADDING-LEFT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-RIGHT'])) {
					$c['padding']['R'] = $this->ConvertSize($properties['PADDING-RIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-BOTTOM'])) {
					$c['padding']['B'] = $this->ConvertSize($properties['PADDING-BOTTOM'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}
				if (isset($properties['PADDING-TOP'])) {
					$c['padding']['T'] = $this->ConvertSize($properties['PADDING-TOP'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				}

				$w = '';
				if (isset($properties['WIDTH'])) {
					$w = $properties['WIDTH'];
				} else if (isset($attr['WIDTH'])) {
					$w = $attr['WIDTH'];
				}
				if ($w) {
					if (strpos($w, '%') && !$this->ignore_table_percents) {
						$c['wpercent'] = $w + 0;
					} // makes 80% -> 80
					else if (!strpos($w, '%') && !$this->ignore_table_widths) {
						$c['w'] = $this->ConvertSize($w, $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
					}
				}

				if (isset($properties['HEIGHT']) && !strpos($properties['HEIGHT'], '%')) {
					$c['h'] = $this->ConvertSize($properties['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
				} else if (isset($attr['HEIGHT']) && !strpos($attr['HEIGHT'], '%'))
					$c['h'] = $this->ConvertSize($attr['HEIGHT'], $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);

				if (isset($properties['WHITE-SPACE'])) {
					if (strtoupper($properties['WHITE-SPACE']) == 'NOWRAP') {
						$c['nowrap'] = 1;
					}
				}
				$properties = array();


				if (isset($attr['TEXT-ROTATE'])) {
					$c['R'] = $attr['TEXT-ROTATE'];
				}
				if (isset($attr['NOWRAP']) && $attr['NOWRAP'])
					$c['nowrap'] = 1;

				$this->cell[$this->row][$this->col] = $c;
				unset($c);
				$this->cell[$this->row][$this->col]['s'] = 0;

				$cs = $rs = 1;
				if (isset($attr['COLSPAN']) && $attr['COLSPAN'] > 1)
					$cs = $this->cell[$this->row][$this->col]['colspan'] = $attr['COLSPAN'];
				if ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] < $this->col + $cs) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] = $this->col + $cs;
				} // following code moved outside if...
				for ($l = $this->col; $l < $this->col + $cs; $l++) {
					if ($l - $this->col)
						$this->cell[$this->row][$l] = 0;
				}
				if (isset($attr['ROWSPAN']) && $attr['ROWSPAN'] > 1)
					$rs = $this->cell[$this->row][$this->col]['rowspan'] = $attr['ROWSPAN'];
				for ($k = $this->row; $k < $this->row + $rs; $k++) {
					for ($l = $this->col; $l < $this->col + $cs; $l++) {
						if ($k - $this->row || $l - $this->col)
							$this->cell[$k][$l] = 0;
					}
				}
				unset($table);
				break;
			/* -- END TABLES -- */
		}//end of switch
	}

// LIST MARKERS	// mPDF 6  Lists
	function _setListMarker($listitemtype, $listitemimage, $listitemposition)
	{
		// if position:inside (and NOT table) - output now as a textbuffer; (so if next is block, will move to new line)
		// else if position:outside (and NOT table) - output in front of first textbuffer output by setting listitem (cf. _saveTextBuffer)
		$e = '';
		$this->listitem = '';
		$spacer = ' ';
		// IMAGE
		if ($listitemimage && $listitemimage != 'none') {
			$listitemimage = trim(preg_replace('/url\(["\']*(.*?)["\']*\)/', '\\1', $listitemimage));

			// ? Restrict maximum height/width of list marker??
			$maxWidth = 100;
			$maxHeight = 100;

			$objattr = array();
			$objattr['margin_top'] = 0;
			$objattr['margin_bottom'] = 0;
			$objattr['margin_left'] = 0;
			$objattr['margin_right'] = 0;
			$objattr['padding_top'] = 0;
			$objattr['padding_bottom'] = 0;
			$objattr['padding_left'] = 0;
			$objattr['padding_right'] = 0;
			$objattr['width'] = 0;
			$objattr['height'] = 0;
			$objattr['border_top']['w'] = 0;
			$objattr['border_bottom']['w'] = 0;
			$objattr['border_left']['w'] = 0;
			$objattr['border_right']['w'] = 0;
			$objattr['visibility'] = 'visible';
			$srcpath = $listitemimage;
			$orig_srcpath = $listitemimage;

			$objattr['vertical-align'] = 'BS'; // vertical alignment of marker (baseline)
			$w = 0;
			$h = 0;

			// Image file
			$info = $this->_getImage($srcpath, true, true, $orig_srcpath);
			if (!$info)
				return;
			if ($info['w'] == 0 && $info['h'] == 0) {
				$info['h'] = $this->ConvertSize('1em', $this->blk[$this->blklvl]['inner_width'], $this->FontSize, false);
			}
			$objattr['file'] = $srcpath;
			//Default width and height calculation if needed
			if ($w == 0 and $h == 0) {
				/* -- IMAGES-WMF -- */
				if ($info['type'] == 'wmf') {
					// WMF units are twips (1/20pt)
					// divide by 20 to get points
					// divide by k to get user units
					$w = abs($info['w']) / (20 * _MPDFK);
					$h = abs($info['h']) / (20 * _MPDFK);
				} else
				/* -- END IMAGES-WMF -- */
				if ($info['type'] == 'svg') {
					// SVG units are pixels
					$w = abs($info['w']) / _MPDFK;
					$h = abs($info['h']) / _MPDFK;
				} else {
					//Put image at default image dpi
					$w = ($info['w'] / _MPDFK) * (72 / $this->img_dpi);
					$h = ($info['h'] / _MPDFK) * (72 / $this->img_dpi);
				}
			}
			// IF WIDTH OR HEIGHT SPECIFIED
			if ($w == 0)
				$w = abs($h * $info['w'] / $info['h']);
			if ($h == 0)
				$h = abs($w * $info['h'] / $info['w']);

			if ($w > $maxWidth) {
				$w = $maxWidth;
				$h = abs($w * $info['h'] / $info['w']);
			}

			if ($h > $maxHeight) {
				$h = $maxHeight;
				$w = abs($h * $info['w'] / $info['h']);
			}
			$objattr['type'] = 'image';
			$objattr['itype'] = $info['type'];

			$objattr['orig_h'] = $info['h'];
			$objattr['orig_w'] = $info['w'];
			/* -- IMAGES-WMF -- */
			if ($info['type'] == 'wmf') {
				$objattr['wmf_x'] = $info['x'];
				$objattr['wmf_y'] = $info['y'];
			} else
			/* -- END IMAGES-WMF -- */
			if ($info['type'] == 'svg') {
				$objattr['wmf_x'] = $info['x'];
				$objattr['wmf_y'] = $info['y'];
			}
			$objattr['height'] = $h;
			$objattr['width'] = $w;
			$objattr['image_height'] = $h;
			$objattr['image_width'] = $w;

			$objattr['dir'] = (isset($this->blk[$this->blklvl]['direction']) ? $this->blk[$this->blklvl]['direction'] : 'ltr');
			$objattr['listmarker'] = true;

			$objattr['listmarkerposition'] = $listitemposition;

			$e = "\xbb\xa4\xactype=image,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
			$this->_saveTextBuffer($e);

			if ($listitemposition == 'inside') {
				$e = $spacer;
				$this->_saveTextBuffer($e);
			}
		}
		// SYMBOL (needs new font)
		else if ($listitemtype == 'disc' || $listitemtype == 'circle' || $listitemtype == 'square') {
			$objattr = array();
			$objattr['type'] = 'listmarker';
			$objattr['listmarkerposition'] = $listitemposition;
			$objattr['width'] = 0;
			$size = $this->ConvertSize($this->list_symbol_size, $this->FontSize);
			$objattr['size'] = $size;
			$objattr['offset'] = $this->ConvertSize($this->list_marker_offset, $this->FontSize);

			if ($listitemposition == 'inside') {
				$objattr['width'] = $size + $objattr['offset'];
			}

			$objattr['height'] = $this->FontSize;
			$objattr['vertical-align'] = 'T';
			$objattr['text'] = $list_item_marker;
			$objattr['dir'] = (isset($this->blk[$this->blklvl]['direction']) ? $this->blk[$this->blklvl]['direction'] : 'ltr');
			$objattr['bullet'] = $listitemtype;
			$objattr['colorarray'] = $this->colorarray;
			$objattr['fontfamily'] = $this->FontFamily;
			$objattr['fontsize'] = $this->FontSize;
			$objattr['fontsizept'] = $this->FontSizePt;
			$objattr['fontstyle'] = $this->FontStyle;

			$e = "\xbb\xa4\xactype=listmarker,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
			$this->listitem = $this->_saveTextBuffer($e, '', '', true); // true returns array
			//	if ($listitemposition == 'inside') {
			//		$e = $spacer;
			//		$this->_saveTextBuffer($e);
			//	}
		}
		// SYMBOL 2 (needs new font)
		else if (preg_match('/U\+([a-fA-F0-9]+)/i', $listitemtype, $m)) {
			if ($this->_charDefined($this->CurrentFont['cw'], hexdec($m[1]))) {
				$list_item_marker = codeHex2utf($m[1]);
			} else {
				$list_item_marker = '-';
			}
			if (preg_match('/rgb\(.*?\)/', $listitemtype, $m)) {
				$list_item_color = $this->ConvertColor($m[0]);
			} else {
				$list_item_color = '';
			}

			// SAVE then SET COLR
			$save_colorarray = $this->colorarray;
			if ($list_item_color) {
				$this->colorarray = $list_item_color;
			}

			if ($listitemposition == 'inside') {
				$e = $list_item_marker . $spacer;
				$this->_saveTextBuffer($e);
			} else {
				$objattr = array();
				$objattr['type'] = 'listmarker';
				$objattr['width'] = 0;
				$objattr['height'] = $this->FontSize;
				$objattr['vertical-align'] = 'T';
				$objattr['text'] = $list_item_marker;
				$objattr['dir'] = (isset($this->blk[$this->blklvl]['direction']) ? $this->blk[$this->blklvl]['direction'] : 'ltr');
				$objattr['colorarray'] = $this->colorarray;
				$objattr['fontfamily'] = $this->FontFamily;
				$objattr['fontsize'] = $this->FontSize;
				$objattr['fontsizept'] = $this->FontSizePt;
				$objattr['fontstyle'] = $this->FontStyle;
				$e = "\xbb\xa4\xactype=listmarker,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
				$this->listitem = $this->_saveTextBuffer($e, '', '', true); // true returns array
			}
			// RESET COLOR
			$this->colorarray = $save_colorarray;
		}
		// TEXT
		else {
			$counter = $this->listcounter[$this->listlvl];
			if ($listitemtype == 'none') {
				return;
			}
			$num = $this->_getStyledNumber($counter, $listitemtype, true);

			if ($listitemposition == 'inside') {
				$e = $num . $this->list_number_suffix . $spacer;
				$this->_saveTextBuffer($e);
			} else {
				if (isset($this->blk[$this->blklvl]['direction']) && $this->blk[$this->blklvl]['direction'] == 'rtl') {
					// REPLACE MIRRORED RTL $this->list_number_suffix  e.g. ) -> (  (NB could use UCDN::$mirror_pairs)
					$m = strtr($this->list_number_suffix, ")]}", "([{") . $num;
				} else {
					$m = $num . $this->list_number_suffix;
				}

				$objattr = array();
				$objattr['type'] = 'listmarker';
				$objattr['width'] = 0;
				$objattr['height'] = $this->FontSize;
				$objattr['vertical-align'] = 'T';
				$objattr['text'] = $m;
				$objattr['dir'] = (isset($this->blk[$this->blklvl]['direction']) ? $this->blk[$this->blklvl]['direction'] : 'ltr');
				$objattr['colorarray'] = $this->colorarray;
				$objattr['fontfamily'] = $this->FontFamily;
				$objattr['fontsize'] = $this->FontSize;
				$objattr['fontsizept'] = $this->FontSizePt;
				$objattr['fontstyle'] = $this->FontStyle;
				$e = "\xbb\xa4\xactype=listmarker,objattr=" . serialize($objattr) . "\xbb\xa4\xac";

				$this->listitem = $this->_saveTextBuffer($e, '', '', true); // true returns array
			}
		}
	}

// mPDF Lists
	function _getListMarkerWidth(&$currblk, &$a, &$i)
	{
		$blt_width = 0;

		$markeroffset = $this->ConvertSize($this->list_marker_offset, $this->FontSize);

		// Get Maximum number in the list
		$maxnum = $this->listcounter[$this->listlvl];
		if ($currblk['list_style_type'] != 'disc' && $currblk['list_style_type'] != 'circle' && $currblk['list_style_type'] != 'square') {
			$lvl = 1;
			for ($j = $i + 2; $j < count($a); $j+=2) {
				$e = $a[$j];
				if (!$e) {
					continue;
				}
				if ($e[0] == '/') { // end tag
					$e = strtoupper(substr($e, 1));
					if ($e == 'OL' || $e == 'UL') {
						if ($lvl == 1) {
							break;
						}
						$lvl--;
					}
				} else { // opening tag
					if (strpos($e, ' ')) {
						$e = substr($e, 0, strpos($e, ' '));
					}
					$e = strtoupper($e);
					if ($e == 'LI') {
						if ($lvl == 1) {
							$maxnum++;
						}
					} else if ($e == 'OL' || $e == 'UL') {
						$lvl++;
					}
				}
			}
		}

		switch ($currblk['list_style_type']) {
			case 'decimal':
			case '1':
				$blt_width = $this->GetStringWidth(str_repeat('5', strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'none':
				$blt_width = 0;
				break;
			case 'upper-alpha':
			case 'upper-latin':
			case 'A':
				$maxnumA = $this->dec2alpha($maxnum, true);
				if ($maxnum < 13) {
					$blt_width = $this->GetStringWidth('D' . $this->list_number_suffix);
				} else {
					$blt_width = $this->GetStringWidth(str_repeat('W', strlen($maxnumA)) . $this->list_number_suffix);
				}
				break;
			case 'lower-alpha':
			case 'lower-latin':
			case 'a':
				$maxnuma = $this->dec2alpha($maxnum, false);
				if ($maxnum < 13) {
					$blt_width = $this->GetStringWidth('b' . $this->list_number_suffix);
				} else {
					$blt_width = $this->GetStringWidth(str_repeat('m', strlen($maxnuma)) . $this->list_number_suffix);
				}
				break;
			case 'upper-roman':
			case 'I':
				if ($maxnum > 87) {
					$bbit = 87;
				} else if ($maxnum > 86) {
					$bbit = 86;
				} else if ($maxnum > 37) {
					$bbit = 38;
				} else if ($maxnum > 36) {
					$bbit = 37;
				} else if ($maxnum > 27) {
					$bbit = 28;
				} else if ($maxnum > 26) {
					$bbit = 27;
				} else if ($maxnum > 17) {
					$bbit = 18;
				} else if ($maxnum > 16) {
					$bbit = 17;
				} else if ($maxnum > 7) {
					$bbit = 8;
				} else if ($maxnum > 6) {
					$bbit = 7;
				} else if ($maxnum > 3) {
					$bbit = 4;
				} else {
					$bbit = $maxnum;
				}
				$maxlnum = $this->dec2roman($bbit, true);
				$blt_width = $this->GetStringWidth($maxlnum . $this->list_number_suffix);
				break;
			case 'lower-roman':
			case 'i':
				if ($maxnum > 87) {
					$bbit = 87;
				} else if ($maxnum > 86) {
					$bbit = 86;
				} else if ($maxnum > 37) {
					$bbit = 38;
				} else if ($maxnum > 36) {
					$bbit = 37;
				} else if ($maxnum > 27) {
					$bbit = 28;
				} else if ($maxnum > 26) {
					$bbit = 27;
				} else if ($maxnum > 17) {
					$bbit = 18;
				} else if ($maxnum > 16) {
					$bbit = 17;
				} else if ($maxnum > 7) {
					$bbit = 8;
				} else if ($maxnum > 6) {
					$bbit = 7;
				} else if ($maxnum > 3) {
					$bbit = 4;
				} else {
					$bbit = $maxnum;
				}
				$maxlnum = $this->dec2roman($bbit, false);
				$blt_width = $this->GetStringWidth($maxlnum . $this->list_number_suffix);
				break;

			case 'disc':
			case 'circle':
			case 'square':
				$size = $this->ConvertSize($this->list_symbol_size, $this->FontSize);
				$offset = $this->ConvertSize($this->list_marker_offset, $this->FontSize);
				$blt_width = $size + $offset;
				break;

			case 'arabic-indic':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0660), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'persian':
			case 'urdu':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x06F0), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'bengali':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x09E6), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'devanagari':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0966), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'gujarati':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0AE6), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'gurmukhi':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0A66), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'kannada':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0CE6), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'malayalam':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(6, 0x0D66), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'oriya':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0B66), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'telugu':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(3, 0x0C66), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'tamil':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(9, 0x0BE6), strlen($maxnum)) . $this->list_number_suffix);
				break;
			case 'thai':
				$blt_width = $this->GetStringWidth(str_repeat($this->dec2other(5, 0x0E50), strlen($maxnum)) . $this->list_number_suffix);
				break;
			default:
				$blt_width = $this->GetStringWidth(str_repeat('5', strlen($maxnum)) . $this->list_number_suffix);
				break;
		}



		return ($blt_width + $markeroffset);
	}

	function CloseTag($tag, &$ahtml, &$ihtml)
	{ // mPDF 6
		//Closing tag
		if ($tag == 'OPTION') {
			$this->selectoption['ACTIVE'] = false;
			$this->lastoptionaltag = '';
		}

		if ($tag == 'TTS' or $tag == 'TTA' or $tag == 'TTZ') {
			if ($this->InlineProperties[$tag]) {
				$this->restoreInlineProperties($this->InlineProperties[$tag]);
			}
			unset($this->InlineProperties[$tag]);
			$ltag = strtolower($tag);
			$this->$ltag = false;
		}


		if ($tag == 'FONT' || $tag == 'SPAN' || $tag == 'CODE' || $tag == 'KBD' || $tag == 'SAMP' || $tag == 'TT' || $tag == 'VAR' || $tag == 'INS' || $tag == 'STRONG' || $tag == 'CITE' || $tag == 'SUB' || $tag == 'SUP' || $tag == 'S' || $tag == 'STRIKE' || $tag == 'DEL' || $tag == 'Q' || $tag == 'EM' || $tag == 'B' || $tag == 'I' || $tag == 'U' | $tag == 'SMALL' || $tag == 'BIG' || $tag == 'ACRONYM' || $tag == 'MARK' || $tag == 'TIME' || $tag == 'PROGRESS' || $tag == 'METER' || $tag == 'BDO' || $tag == 'BDI'
		) {

			$annot = false; // mPDF 6
			$bdf = false; // mPDF 6
			// mPDF 5.7.3 Inline tags
			if ($tag == 'PROGRESS' || $tag == 'METER') {
				if (isset($this->InlineProperties[$tag]) && $this->InlineProperties[$tag]) {
					$this->restoreInlineProperties($this->InlineProperties[$tag]);
				}
				unset($this->InlineProperties[$tag]);
				if (isset($this->InlineAnnots[$tag]) && $this->InlineAnnots[$tag]) {
					$annot = $this->InlineAnnots[$tag];
				} // *ANNOTATIONS*
				unset($this->InlineAnnots[$tag]); // *ANNOTATIONS*
			} else {
				if (isset($this->InlineProperties[$tag]) && count($this->InlineProperties[$tag])) {
					$tmpProps = array_pop($this->InlineProperties[$tag]); // mPDF 5.7.4
					$this->restoreInlineProperties($tmpProps);
				}
				if (isset($this->InlineAnnots[$tag]) && count($this->InlineAnnots[$tag])) {  // *ANNOTATIONS*
					$annot = array_pop($this->InlineAnnots[$tag]);  // *ANNOTATIONS*
				} // *ANNOTATIONS*
				if (isset($this->InlineBDF[$tag]) && count($this->InlineBDF[$tag])) {  // mPDF 6
					$bdfarr = array_pop($this->InlineBDF[$tag]);
					$bdf = $bdfarr[0];
				}
			}

			/* -- ANNOTATIONS -- */
			if ($annot) { // mPDF 6
				if ($this->tableLevel) { // *TABLES*
					$this->cell[$this->row][$this->col]['textbuffer'][] = array($annot); // *TABLES*
				} // *TABLES*
				else { // *TABLES*
					$this->textbuffer[] = array($annot);
				} // *TABLES*
			}
			/* -- END ANNOTATIONS -- */

			// mPDF 6 bidi
			// mPDF 6 Bidirectional formatting for inline elements
			if ($bdf) {
				$popf = $this->_setBidiCodes('end', $bdf);
				$this->OTLdata = array();
				if ($this->tableLevel) {
					$this->_saveCellTextBuffer($popf);
				} else {
					$this->_saveTextBuffer($popf);
				}
			}
		} // End of (most) Inline elements eg SPAN


		if ($tag == 'METER' || $tag == 'PROGRESS') {
			$this->ignorefollowingspaces = false;
			$this->inMeter = false;
		}


		if ($tag == 'A') {
			$this->HREF = '';
			if (isset($this->InlineProperties['A'])) {
				$this->restoreInlineProperties($this->InlineProperties['A']);
			}
			unset($this->InlineProperties['A']);
		}

		if ($tag == 'LEGEND') {
			if (count($this->textbuffer) && !$this->tableLevel) {
				$leg = $this->textbuffer[(count($this->textbuffer) - 1)];
				unset($this->textbuffer[(count($this->textbuffer) - 1)]);
				$this->textbuffer = array_values($this->textbuffer);
				$this->blk[$this->blklvl]['border_legend'] = $leg;
				$this->blk[$this->blklvl]['margin_top'] += ($leg[11] / 2) / _MPDFK;
				$this->blk[$this->blklvl]['padding_top'] += ($leg[11] / 2) / _MPDFK;
			}
			if (isset($this->InlineProperties['LEGEND'])) {
				$this->restoreInlineProperties($this->InlineProperties['LEGEND']);
			}
			unset($this->InlineProperties['LEGEND']);
			$this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
		}



		/* -- FORMS -- */
		// *********** FORM ELEMENTS ********************

		if ($tag == 'TEXTAREA') {
			$this->ignorefollowingspaces = false;
			$this->specialcontent = '';
			if ($this->InlineProperties[$tag]) {
				$this->restoreInlineProperties($this->InlineProperties[$tag]);
			}
			unset($this->InlineProperties[$tag]);
		}


		if ($tag == 'SELECT') {
			$this->ignorefollowingspaces = false;
			$this->lastoptionaltag = '';
			$texto = '';
			$OTLdata = false;
			if (isset($this->selectoption['SELECTED'])) {
				$texto = $this->selectoption['SELECTED'];
			}
			if (isset($this->selectoption['SELECTED-OTLDATA'])) {
				$OTLdata = $this->selectoption['SELECTED-OTLDATA'];
			}

			if ($this->useActiveForms) {
				$w = $this->selectoption['MAXWIDTH'];
			} else {
				$w = $this->GetStringWidth($texto, true, $OTLdata);
			}
			if ($w == 0) {
				$w = 5;
			}
			$objattr['type'] = 'select';
			$objattr['text'] = $texto;
			$objattr['OTLdata'] = $OTLdata;
			if (isset($this->selectoption['NAME'])) {
				$objattr['fieldname'] = $this->selectoption['NAME'];
			}
			if (isset($this->selectoption['READONLY'])) {
				$objattr['readonly'] = true;
			}
			if (isset($this->selectoption['REQUIRED'])) {
				$objattr['required'] = true;
			}
			if (isset($this->selectoption['SPELLCHECK'])) {
				$objattr['spellcheck'] = true;
			}
			if (isset($this->selectoption['EDITABLE'])) {
				$objattr['editable'] = true;
			}
			if (isset($this->selectoption['ONCHANGE'])) {
				$objattr['onChange'] = $this->selectoption['ONCHANGE'];
			}
			if (isset($this->selectoption['ITEMS'])) {
				$objattr['items'] = $this->selectoption['ITEMS'];
			}
			if (isset($this->selectoption['MULTIPLE'])) {
				$objattr['multiple'] = $this->selectoption['MULTIPLE'];
			}
			if (isset($this->selectoption['DISABLED'])) {
				$objattr['disabled'] = $this->selectoption['DISABLED'];
			}
			if (isset($this->selectoption['TITLE'])) {
				$objattr['title'] = $this->selectoption['TITLE'];
			}
			if (isset($this->selectoption['COLOR'])) {
				$objattr['color'] = $this->selectoption['COLOR'];
			}
			if (isset($this->selectoption['SIZE'])) {
				$objattr['size'] = $this->selectoption['SIZE'];
			}
			if (isset($objattr['size']) && $objattr['size'] > 1) {
				$rows = $objattr['size'];
			} else {
				$rows = 1;
			}

			$objattr['fontfamily'] = $this->FontFamily;
			$objattr['fontsize'] = $this->FontSizePt;

			$objattr['width'] = $w + ($this->mpdfform->form_element_spacing['select']['outer']['h'] * 2) + ($this->mpdfform->form_element_spacing['select']['inner']['h'] * 2) + ($this->FontSize * 1.4);
			$objattr['height'] = ($this->FontSize * $rows) + ($this->mpdfform->form_element_spacing['select']['outer']['v'] * 2) + ($this->mpdfform->form_element_spacing['select']['inner']['v'] * 2);
			$e = "\xbb\xa4\xactype=select,objattr=" . serialize($objattr) . "\xbb\xa4\xac";

			// Clear properties - tidy up
			$properties = array();

			// Output it to buffers
			if ($this->tableLevel) { // *TABLES*
				$this->_saveCellTextBuffer($e, $this->HREF);
				$this->cell[$this->row][$this->col]['s'] += $objattr['width']; // *TABLES*
			} // *TABLES*
			else { // *TABLES*
				$this->_saveTextBuffer($e, $this->HREF);
			} // *TABLES*

			$this->selectoption = array();
			$this->specialcontent = '';

			if ($this->InlineProperties[$tag]) {
				$this->restoreInlineProperties($this->InlineProperties[$tag]);
			}
			unset($this->InlineProperties[$tag]);
		}
		/* -- END FORMS -- */


		// *********** BLOCKS ********************
		// mPDF 6  Lists
		if ($tag == 'P' || $tag == 'DIV' || $tag == 'H1' || $tag == 'H2' || $tag == 'H3' || $tag == 'H4' || $tag == 'H5' || $tag == 'H6' || $tag == 'PRE' || $tag == 'FORM' || $tag == 'ADDRESS' || $tag == 'BLOCKQUOTE' || $tag == 'CENTER' || $tag == 'DT' || $tag == 'DD' || $tag == 'DL' || $tag == 'CAPTION' || $tag == 'FIELDSET' || $tag == 'UL' || $tag == 'OL' || $tag == 'LI' || $tag == 'ARTICLE' || $tag == 'ASIDE' || $tag == 'FIGURE' || $tag == 'FIGCAPTION' || $tag == 'FOOTER' || $tag == 'HEADER' || $tag == 'HGROUP' || $tag == 'MAIN' || $tag == 'NAV' || $tag == 'SECTION' || $tag == 'DETAILS' || $tag == 'SUMMARY'
		) {

			// mPDF 6 bidi
			// Block
			// If unicode-bidi set, any embedding levels, isolates, or overrides started by this box are closed
			if (isset($this->blk[$this->blklvl]['bidicode'])) {
				$blockpost = $this->_setBidiCodes('end', $this->blk[$this->blklvl]['bidicode']);
				if ($blockpost) {
					$this->OTLdata = array();
					if ($this->tableLevel) {
						$this->_saveCellTextBuffer($blockpost);
					} else {
						$this->_saveTextBuffer($blockpost);
					}
				}
			}

			$this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
			$this->blockjustfinished = true;

			$this->lastblockbottommargin = $this->blk[$this->blklvl]['margin_bottom'];
			// mPDF 6  Lists
			if ($tag == 'UL' || $tag == 'OL') {
				if ($this->listlvl > 0 && $this->tableLevel) {
					if (isset($this->listtype[$this->listlvl]))
						unset($this->listtype[$this->listlvl]);
				}
				$this->listlvl--;
				$this->listitem = array();
			}
			if ($tag == 'LI') {
				$this->listitem = array();
			}

			if (preg_match('/^H\d/', $tag) && !$this->tableLevel && !$this->writingToC) {
				if (isset($this->h2toc[$tag]) || isset($this->h2bookmarks[$tag])) {
					$content = '';
					if (count($this->textbuffer) == 1) {
						$content = $this->textbuffer[0][0];
					} else {
						for ($i = 0; $i < count($this->textbuffer); $i++) {
							if (substr($this->textbuffer[$i][0], 0, 3) != "\xbb\xa4\xac") { //inline object
								$content .= $this->textbuffer[$i][0];
							}
						}
					}
					/* -- TOC -- */
					if (isset($this->h2toc[$tag])) {
						$objattr = array();
						$objattr['type'] = 'toc';
						$objattr['toclevel'] = $this->h2toc[$tag];
						$objattr['CONTENT'] = htmlspecialchars($content);
						$e = "\xbb\xa4\xactype=toc,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
						array_unshift($this->textbuffer, array($e));
					}
					/* -- END TOC -- */
					/* -- BOOKMARKS -- */
					if (isset($this->h2bookmarks[$tag])) {
						$objattr = array();
						$objattr['type'] = 'bookmark';
						$objattr['bklevel'] = $this->h2bookmarks[$tag];
						$objattr['CONTENT'] = $content;
						$e = "\xbb\xa4\xactype=toc,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
						array_unshift($this->textbuffer, array($e));
					}
					/* -- END BOOKMARKS -- */
				}
			}

			/* -- TABLES -- */
			if ($this->tableLevel) {
				if ($this->linebreakjustfinished) {
					$this->blockjustfinished = false;
				}
				if (isset($this->InlineProperties['BLOCKINTABLE'])) {
					if ($this->InlineProperties['BLOCKINTABLE']) {
						$this->restoreInlineProperties($this->InlineProperties['BLOCKINTABLE']);
					}
					unset($this->InlineProperties['BLOCKINTABLE']);
				}
				if ($tag == 'PRE') {
					$this->ispre = false;
				}
				return;
			}
			/* -- END TABLES -- */
			$this->lastoptionaltag = '';
			$this->divbegin = false;

			$this->linebreakjustfinished = false;

			$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];

			/* -- CSS-FLOAT -- */
			// If float contained in a float, need to extend bottom to allow for it
			$currpos = $this->page * 1000 + $this->y;
			if (isset($this->blk[$this->blklvl]['float_endpos']) && $this->blk[$this->blklvl]['float_endpos'] > $currpos) {
				$old_page = $this->page;
				$new_page = intval($this->blk[$this->blklvl]['float_endpos'] / 1000);
				if ($old_page != $new_page) {
					$s = $this->PrintPageBackgrounds();
					// Writes after the marker so not overwritten later by page background etc.
					$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', '\\1' . "\n" . $s . "\n", $this->pages[$this->page]);
					$this->pageBackgrounds = array();
					$this->page = $new_page;
					$this->ResetMargins();
					$this->Reset();
					$this->pageoutput[$this->page] = array();
				}
				$this->y = (($this->blk[$this->blklvl]['float_endpos'] * 1000) % 1000000) / 1000; // mod changes operands to integers before processing
			}
			/* -- END CSS-FLOAT -- */


			//Print content
			if ($this->lastblocklevelchange == 1) {
				$blockstate = 3;
			} // Top & bottom margins/padding
			else if ($this->lastblocklevelchange == -1) {
				$blockstate = 2;
			} // Bottom margins/padding only
			else {
				$blockstate = 0;
			}
			// called from after e.g. </table> </div> </div> ...    Outputs block margin/border and padding
			if (count($this->textbuffer) && $this->textbuffer[count($this->textbuffer) - 1]) {
				if (substr($this->textbuffer[count($this->textbuffer) - 1][0], 0, 3) != "\xbb\xa4\xac") { // not special content
					// Right trim last content and adjust OTLdata
					if (preg_match('/[ ]+$/', $this->textbuffer[count($this->textbuffer) - 1][0], $m)) {
						$strip = strlen($m[0]);
						$this->textbuffer[count($this->textbuffer) - 1][0] = substr($this->textbuffer[count($this->textbuffer) - 1][0], 0, (strlen($this->textbuffer[count($this->textbuffer) - 1][0]) - $strip));
						/* -- OTL -- */
						if (isset($this->CurrentFont['useOTL']) && $this->CurrentFont['useOTL']) {
							$this->otl->trimOTLdata($this->textbuffer[count($this->textbuffer) - 1][18], false, true); // mPDF 6  ZZZ99K
						}
						/* -- END OTL -- */
					}
				}
			}

			if (count($this->textbuffer) == 0 && $this->lastblocklevelchange != 0) {
				//$this->newFlowingBlock( $this->blk[$this->blklvl]['width'],$this->lineheight,'',false,2,true, (isset($this->blk[$this->blklvl]['direction']) ? $this->blk[$this->blklvl]['direction'] : 'ltr'));
				$this->newFlowingBlock($this->blk[$this->blklvl]['width'], $this->lineheight, '', false, $blockstate, true, (isset($this->blk[$this->blklvl]['direction']) ? $this->blk[$this->blklvl]['direction'] : 'ltr'));
				$this->finishFlowingBlock(true); // true = END of flowing block
				$this->PaintDivBB('', $blockstate);
			} else {
				$this->printbuffer($this->textbuffer, $blockstate);
			}


			$this->textbuffer = array();

			if ($this->kwt) {
				$this->kwt_height = $this->y - $this->kwt_y0;
			}

			/* -- CSS-IMAGE-FLOAT -- */
			$this->printfloatbuffer();
			/* -- END CSS-IMAGE-FLOAT -- */

			if ($tag == 'PRE') {
				$this->ispre = false;
			}

			/* -- CSS-FLOAT -- */
			if ($this->blk[$this->blklvl]['float'] == 'R') {
				// If width not set, here would need to adjust and output buffer
				$s = $this->PrintPageBackgrounds();
				// Writes after the marker so not overwritten later by page background etc.
				$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', '\\1' . "\n" . $s . "\n", $this->pages[$this->page]);
				$this->pageBackgrounds = array();
				$this->Reset();
				$this->pageoutput[$this->page] = array();

				for ($i = ($this->blklvl - 1); $i >= 0; $i--) {
					if (isset($this->blk[$i]['float_endpos'])) {
						$this->blk[$i]['float_endpos'] = max($this->blk[$i]['float_endpos'], ($this->page * 1000 + $this->y));
					} else {
						$this->blk[$i]['float_endpos'] = $this->page * 1000 + $this->y;
					}
				}

				$this->floatDivs[] = array(
					'side' => 'R',
					'startpage' => $this->blk[$this->blklvl]['startpage'],
					'y0' => $this->blk[$this->blklvl]['float_start_y'],
					'startpos' => ($this->blk[$this->blklvl]['startpage'] * 1000 + $this->blk[$this->blklvl]['float_start_y']),
					'endpage' => $this->page,
					'y1' => $this->y,
					'endpos' => ($this->page * 1000 + $this->y),
					'w' => $this->blk[$this->blklvl]['float_width'],
					'blklvl' => $this->blklvl,
					'blockContext' => $this->blk[$this->blklvl - 1]['blockContext']
				);

				$this->y = $this->blk[$this->blklvl]['float_start_y'];
				$this->page = $this->blk[$this->blklvl]['startpage'];
				$this->ResetMargins();
				$this->pageoutput[$this->page] = array();
			}
			if ($this->blk[$this->blklvl]['float'] == 'L') {
				// If width not set, here would need to adjust and output buffer
				$s = $this->PrintPageBackgrounds();
				// Writes after the marker so not overwritten later by page background etc.
				$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS' . $this->uniqstr . ')/', '\\1' . "\n" . $s . "\n", $this->pages[$this->page]);
				$this->pageBackgrounds = array();
				$this->Reset();
				$this->pageoutput[$this->page] = array();

				for ($i = ($this->blklvl - 1); $i >= 0; $i--) {
					if (isset($this->blk[$i]['float_endpos'])) {
						$this->blk[$i]['float_endpos'] = max($this->blk[$i]['float_endpos'], ($this->page * 1000 + $this->y));
					} else {
						$this->blk[$i]['float_endpos'] = $this->page * 1000 + $this->y;
					}
				}

				$this->floatDivs[] = array(
					'side' => 'L',
					'startpage' => $this->blk[$this->blklvl]['startpage'],
					'y0' => $this->blk[$this->blklvl]['float_start_y'],
					'startpos' => ($this->blk[$this->blklvl]['startpage'] * 1000 + $this->blk[$this->blklvl]['float_start_y']),
					'endpage' => $this->page,
					'y1' => $this->y,
					'endpos' => ($this->page * 1000 + $this->y),
					'w' => $this->blk[$this->blklvl]['float_width'],
					'blklvl' => $this->blklvl,
					'blockContext' => $this->blk[$this->blklvl - 1]['blockContext']
				);

				$this->y = $this->blk[$this->blklvl]['float_start_y'];
				$this->page = $this->blk[$this->blklvl]['startpage'];
				$this->ResetMargins();
				$this->pageoutput[$this->page] = array();
			}
			/* -- END CSS-FLOAT -- */

			if (isset($this->blk[$this->blklvl]['visibility']) && $this->blk[$this->blklvl]['visibility'] != 'visible') {
				$this->SetVisibility('visible');
			}

			if (isset($this->blk[$this->blklvl]['page_break_after'])) {
				$page_break_after = $this->blk[$this->blklvl]['page_break_after'];
			} else {
				$page_break_after = '';
			}

			//Reset values
			$this->Reset();

			if (isset($this->blk[$this->blklvl]['z-index']) && $this->blk[$this->blklvl]['z-index'] > 0) {
				$this->EndLayer();
			}

			// mPDF 6 page-break-inside:avoid
			if ($this->blk[$this->blklvl]['keep_block_together']) {
				$movepage = false;
				// If page-break-inside:avoid section has broken to new page but fits on one side - then move:
				if (($this->page - $this->kt_p00) == 1 && $this->y < $this->kt_y00) {
					$movepage = true;
				}
				if (($this->page - $this->kt_p00) > 0) {
					for ($i = $this->page; $i > $this->kt_p00; $i--) {
						unset($this->pages[$i]);
						if (isset($this->blk[$this->blklvl]['bb_painted'][$i])) {
							unset($this->blk[$this->blklvl]['bb_painted'][$i]);
						}
						if (isset($this->blk[$this->blklvl]['marginCorrected'][$i])) {
							unset($this->blk[$this->blklvl]['marginCorrected'][$i]);
						}
						if (isset($this->pageoutput[$i])) {
							unset($this->pageoutput[$i]);
						}
					}
					$this->page = $this->kt_p00;
				}
				$this->keep_block_together = 0;
				$this->pageoutput[$this->page] = array();

				$this->y = $this->kt_y00;
				$ihtml = $this->blk[$this->blklvl]['array_i'] - 1;

				$ahtml[$ihtml + 1] .= ' pagebreakavoidchecked="true";'; // avoid re-iterating; read in OpenTag()

				unset($this->blk[$this->blklvl]);
				$this->blklvl--;

				for ($blklvl = 1; $blklvl <= $this->blklvl; $blklvl++) {
					$this->blk[$blklvl]['y0'] = $this->blk[$blklvl]['initial_y0'];
					$this->blk[$blklvl]['x0'] = $this->blk[$blklvl]['initial_x0'];
					$this->blk[$blklvl]['startpage'] = $this->blk[$blklvl]['initial_startpage'];
				}

				if (isset($this->blk[$this->blklvl]['x0'])) {
					$this->x = $this->blk[$this->blklvl]['x0'];
				} else {
					$this->x = $this->lMargin;
				}

				$this->lastblocklevelchange = 0;
				$this->ResetMargins();
				if ($movepage) {
					$this->AddPage();
				}
				return;
			}

			if ($this->blklvl > 0) { // ==0 SHOULDN'T HAPPEN - NOT XHTML
				if ($this->blk[$this->blklvl]['tag'] == $tag) {
					unset($this->blk[$this->blklvl]);
					$this->blklvl--;
				}
				//else { echo $tag; exit; }	// debug - forces error if incorrectly nested html tags
			}

			$this->lastblocklevelchange = -1;
			// Reset Inline-type properties
			if (isset($this->blk[$this->blklvl]['InlineProperties'])) {
				$this->restoreInlineProperties($this->blk[$this->blklvl]['InlineProperties']);
			}

			$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];

			if (!$this->tableLevel && $page_break_after) {
				$save_blklvl = $this->blklvl;
				$save_blk = $this->blk;
				$save_silp = $this->saveInlineProperties();
				$save_ilp = $this->InlineProperties;
				$save_bflp = $this->InlineBDF;
				$save_bflpc = $this->InlineBDFctr; // mPDF 6
				// mPDF 6 pagebreaktype
				$startpage = $this->page;
				$pagebreaktype = $this->defaultPagebreakType;
				if ($this->ColActive) {
					$pagebreaktype = 'cloneall';
				}

				// mPDF 6 pagebreaktype
				$this->_preForcedPagebreak($pagebreaktype);

				if ($page_break_after == 'RIGHT') {
					$this->AddPage($this->CurOrientation, 'NEXT-ODD', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
				} else if ($page_break_after == 'LEFT') {
					$this->AddPage($this->CurOrientation, 'NEXT-EVEN', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
				} else {
					$this->AddPage($this->CurOrientation, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
				}

				// mPDF 6 pagebreaktype
				$this->_postForcedPagebreak($pagebreaktype, $startpage, $save_blk, $save_blklvl);

				$this->InlineProperties = $save_ilp;
				$this->InlineBDF = $save_bflp;
				$this->InlineBDFctr = $save_bflpc; // mPDF 6
				$this->restoreInlineProperties($save_silp);
			}
			// mPDF 6 bidi
			// Block
			// If unicode-bidi set, any embedding levels, isolates, or overrides reopened in the continuing block
			if (isset($this->blk[$this->blklvl]['bidicode'])) {
				$blockpre = $this->_setBidiCodes('start', $this->blk[$this->blklvl]['bidicode']);
				if ($blockpre) {
					$this->OTLdata = array();
					if ($this->tableLevel) {
						$this->_saveCellTextBuffer($blockpre);
					} else {
						$this->_saveTextBuffer($blockpre);
					}
				}
			}
		}


		/* -- TABLES -- */

		if ($tag == 'TH')
			$this->SetStyle('B', false);

		if (($tag == 'TH' or $tag == 'TD') && $this->tableLevel) {
			$this->lastoptionaltag = 'TR';
			unset($this->cssmgr->tablecascadeCSS[$this->cssmgr->tbCSSlvl]);
			$this->cssmgr->tbCSSlvl--;
			if (!$this->tdbegin) {
				return;
			}
			$this->tdbegin = false;
			// Added for correct calculation of cell column width - otherwise misses the last line if not end </p> etc.
			if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
				if (!is_array($this->cell[$this->row][$this->col])) {
					$this->Error("You may have an error in your HTML code e.g. &lt;/td&gt;&lt;/td&gt;");
				}
				$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
			} elseif ($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
				$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
			}

			// Remove last <br> if at end of cell
			if (isset($this->cell[$this->row][$this->col]['textbuffer'])) {
				$ntb = count($this->cell[$this->row][$this->col]['textbuffer']);
			} else {
				$ntb = 0;
			}
			if ($ntb > 1 && $this->cell[$this->row][$this->col]['textbuffer'][$ntb - 1][0] == "\n") {
				unset($this->cell[$this->row][$this->col]['textbuffer'][$ntb - 1]);
			}

			if ($this->tablethead) {
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_thead'][$this->row] = true;
				if ($this->tableLevel == 1) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['headernrows'] = max($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['headernrows'], ($this->row + 1));
				}
			}
			if ($this->tabletfoot) {
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'][$this->row] = true;
				if ($this->tableLevel == 1) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['footernrows'] = max($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['footernrows'], ($this->row + 1 - $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['headernrows']));
				}
			}
			$this->Reset();
		}

		if ($tag == 'TR' && $this->tableLevel) {
			// If Border set on TR - Update right border
			if (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trborder-left'][$this->row])) {
				$c = & $this->cell[$this->row][$this->col];
				if ($c) {
					if ($this->packTableData) {
						$cell = $this->_unpackCellBorder($c['borderbin']);
					} else {
						$cell = $c;
					}
					$cell['border_details']['R'] = $this->border_details($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trborder-right'][$this->row]);
					$this->setBorder($cell['border'], _BORDER_RIGHT, $cell['border_details']['R']['s']);
					if ($this->packTableData) {
						$c['borderbin'] = $this->_packCellBorder($cell);
						unset($c['border']);
						unset($c['border_details']);
					} else {
						$c = $cell;
					}
				}
			}
			$this->lastoptionaltag = '';
			unset($this->cssmgr->tablecascadeCSS[$this->cssmgr->tbCSSlvl]);
			$this->cssmgr->tbCSSlvl--;
			$this->trow_text_rotate = '';
			$this->tabletheadjustfinished = false;
		}

		if ($tag == 'TBODY') {
			$this->lastoptionaltag = '';
			unset($this->cssmgr->tablecascadeCSS[$this->cssmgr->tbCSSlvl]);
			$this->cssmgr->tbCSSlvl--;
		}

		if ($tag == 'THEAD') {
			$this->lastoptionaltag = '';
			unset($this->cssmgr->tablecascadeCSS[$this->cssmgr->tbCSSlvl]);
			$this->cssmgr->tbCSSlvl--;
			$this->tablethead = 0;
			$this->tabletheadjustfinished = true;
			$this->ResetStyles();
			$this->thead_font_weight = '';
			$this->thead_font_style = '';
			$this->thead_font_smCaps = '';

			$this->thead_valign_default = '';
			$this->thead_textalign_default = '';
		}

		if ($tag == 'TFOOT') {
			$this->lastoptionaltag = '';
			unset($this->cssmgr->tablecascadeCSS[$this->cssmgr->tbCSSlvl]);
			$this->cssmgr->tbCSSlvl--;
			$this->tabletfoot = 0;
			$this->ResetStyles();
			$this->tfoot_font_weight = '';
			$this->tfoot_font_style = '';
			$this->tfoot_font_smCaps = '';

			$this->tfoot_valign_default = '';
			$this->tfoot_textalign_default = '';
		}

		if ($tag == 'TABLE') { // TABLE-END (
			if ($this->progressBar) {
				$this->UpdateProgressBar(1, '', 'TABLE');
			} // *PROGRESS-BAR*
			if ($this->progressBar) {
				$this->UpdateProgressBar(7, 0, '');
			} // *PROGRESS-BAR*
			$this->lastoptionaltag = '';
			unset($this->cssmgr->tablecascadeCSS[$this->cssmgr->tbCSSlvl]);
			$this->cssmgr->tbCSSlvl--;
			$this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
			// mPDF 5.7.3
			// In case a colspan (on a row after first row) exceeded number of columns in table
			for ($k = 0; $k < $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr']; $k++) {
				for ($l = 0; $l < $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc']; $l++) {
					if (!isset($this->cell[$k][$l])) {
						for ($n = $l - 1; $n >= 0; $n--) {
							if (isset($this->cell[$k][$n]) && $this->cell[$k][$n] != 0) {
								break;
							}
						}
						$this->cell[$k][$l] = array(
							'a' => 'C',
							'va' => 'M',
							'R' => false,
							'nowrap' => false,
							'bgcolor' => false,
							'padding' => array('L' => false, 'R' => false, 'T' => false, 'B' => false),
							'gradient' => false,
							's' => 0,
							'maxs' => 0,
							'textbuffer' => array(),
							'dfs' => $this->FontSize,
						);

						if (!$this->simpleTables) {
							$this->cell[$k][$l]['border'] = 0;
							$this->cell[$k][$l]['border_details']['R'] = array('s' => 0, 'w' => 0, 'c' => false, 'style' => 'none', 'dom' => 0);
							$this->cell[$k][$l]['border_details']['L'] = array('s' => 0, 'w' => 0, 'c' => false, 'style' => 'none', 'dom' => 0);
							$this->cell[$k][$l]['border_details']['T'] = array('s' => 0, 'w' => 0, 'c' => false, 'style' => 'none', 'dom' => 0);
							$this->cell[$k][$l]['border_details']['B'] = array('s' => 0, 'w' => 0, 'c' => false, 'style' => 'none', 'dom' => 0);
							$this->cell[$k][$l]['border_details']['mbw'] = array('BL' => 0, 'BR' => 0, 'RT' => 0, 'RB' => 0, 'TL' => 0, 'TR' => 0, 'LT' => 0, 'LB' => 0);
							if ($this->packTableData) {
								$this->cell[$k][$l]['borderbin'] = $this->_packCellBorder($this->cell[$k][$l]);
								unset($this->cell[$k][$l]['border']);
								unset($this->cell[$k][$l]['border_details']);
							}
						}
					}
				}
			}
			$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] = $this->cell;
			$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['wc'] = array_pad(array(), $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'], array('miw' => 0, 'maw' => 0));
			$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['hr'] = array_pad(array(), $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr'], 0);

			// Move table footer <tfoot> row to end of table
			if (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot']) && count($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'])) {
				$tfrows = array();
				foreach ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'] AS $r => $val) {
					if ($val) {
						$tfrows[] = $r;
					}
				}
				$temp = array();
				$temptf = array();
				foreach ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] AS $k => $row) {
					if (in_array($k, $tfrows)) {
						$temptf[] = $row;
					} else {
						$temp[] = $row;
					}
				}
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'] = array();
				for ($i = count($temp); $i < (count($temp) + count($temptf)); $i++) {
					$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'][$i] = true;
				}
				// Update nestedpos row references
				if (isset($this->table[($this->tableLevel + 1)]) && count($this->table[($this->tableLevel + 1)])) {
					foreach ($this->table[($this->tableLevel + 1)] AS $nid => $nested) {
						$this->table[($this->tableLevel + 1)][$nid]['nestedpos'][0] -= count($temptf);
					}
				}
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] = array_merge($temp, $temptf);

				// Update other arays set on row number
				// [trbackground-images] [trgradients]
				$temptrbgi = array();
				$temptrbgg = array();
				$temptrbgc = array();
				if (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][-1])) {
					$temptrbgc[-1] = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][-1];
				}
				for ($k = 0; $k < $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr']; $k++) {
					if (!in_array($k, $tfrows)) {
						$temptrbgi[] = (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trbackground-images'][$k]) ? $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trbackground-images'][$k] : null);
						$temptrbgg[] = (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trgradients'][$k]) ? $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trgradients'][$k] : null);
						$temptrbgc[] = (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$k]) ? $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$k] : null);
					}
				}
				for ($k = 0; $k < $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr']; $k++) {
					if (in_array($k, $tfrows)) {
						$temptrbgi[] = (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trbackground-images'][$k]) ? $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trbackground-images'][$k] : null);
						$temptrbgg[] = (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trgradients'][$k]) ? $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trgradients'][$k] : null);
						$temptrbgc[] = (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$k]) ? $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$k] : null);
					}
				}
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trbackground-images'] = $temptrbgi;
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['trgradients'] = $temptrbgg;
				$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'] = $temptrbgc;
				// Should Update all other arays set on row number, but cell properties have been set so not needed
				// [bgcolor] [trborder-left] [trborder-right] [trborder-top] [trborder-bottom]
			}

			if ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['direction'] == 'rtl') {
				$this->_reverseTableDir($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]);
			}

			// Fix Borders *********************************************
			$this->_fixTableBorders($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]);

			if ($this->progressBar) {
				$this->UpdateProgressBar(7, 10, ' ');
			} // *PROGRESS-BAR*

			if ($this->ColActive) {
				$this->table_rotate = 0;
			} // *COLUMNS*
			if ($this->table_rotate <> 0) {
				$this->tablebuffer = '';
				// Max width for rotated table
				$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 1);
				$this->tbrot_maxh = $this->blk[$this->blklvl]['inner_width'];  // Max width for rotated table
				$this->tbrot_align = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['a'];
			}
			$this->shrin_k = 1;

			if ($this->shrink_tables_to_fit < 1) {
				$this->shrink_tables_to_fit = 1;
			}
			if (!$this->shrink_this_table_to_fit) {
				$this->shrink_this_table_to_fit = $this->shrink_tables_to_fit;
			}

			if ($this->tableLevel > 1) {
				// deal with nested table

				$this->_tableColumnWidth($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]], true);

				$tmiw = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['miw'];
				$tmaw = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['maw'];
				$tl = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['tl'];

				// Go down to lower table level
				$this->tableLevel--;

				// Reset lower level table
				$this->base_table_properties = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['baseProperties'];
				// mPDF 5.7.3
				$this->default_font = $this->base_table_properties['FONT-FAMILY'];
				$this->SetFont($this->default_font, '', 0, false);
				$this->default_font_size = $this->ConvertSize($this->base_table_properties['FONT-SIZE']) * (_MPDFK);
				$this->SetFontSize($this->default_font_size, false);

				$this->cell = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'];
				if (isset($this->cell['PARENTCELL'])) {
					if ($this->cell['PARENTCELL']) {
						$this->restoreInlineProperties($this->cell['PARENTCELL']);
					}
					unset($this->cell['PARENTCELL']);
				}
				$this->row = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currrow'];
				$this->col = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currcol'];
				$objattr = array();
				$objattr['type'] = 'nestedtable';
				$objattr['nestedcontent'] = $this->tbctr[($this->tableLevel + 1)];
				$objattr['table'] = $this->tbctr[$this->tableLevel];
				$objattr['row'] = $this->row;
				$objattr['col'] = $this->col;
				$objattr['level'] = $this->tableLevel;
				$e = "\xbb\xa4\xactype=nestedtable,objattr=" . serialize($objattr) . "\xbb\xa4\xac";
				$this->_saveCellTextBuffer($e);
				$this->cell[$this->row][$this->col]['s'] += $tl;
				if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
					$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
				} elseif ($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
					$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
				}
				$this->cell[$this->row][$this->col]['s'] = 0; // reset
				if ((isset($this->cell[$this->row][$this->col]['nestedmaw']) && $this->cell[$this->row][$this->col]['nestedmaw'] < $tmaw) || !isset($this->cell[$this->row][$this->col]['nestedmaw'])) {
					$this->cell[$this->row][$this->col]['nestedmaw'] = $tmaw;
				}
				if ((isset($this->cell[$this->row][$this->col]['nestedmiw']) && $this->cell[$this->row][$this->col]['nestedmiw'] < $tmiw) || !isset($this->cell[$this->row][$this->col]['nestedmiw'])) {
					$this->cell[$this->row][$this->col]['nestedmiw'] = $tmiw;
				}
				$this->tdbegin = true;
				$this->nestedtablejustfinished = true;
				$this->ignorefollowingspaces = true;
				return;
			}
			$this->cMarginL = 0;
			$this->cMarginR = 0;
			$this->cMarginT = 0;
			$this->cMarginB = 0;
			$this->cellPaddingL = 0;
			$this->cellPaddingR = 0;
			$this->cellPaddingT = 0;
			$this->cellPaddingB = 0;

			if (isset($this->table[1][1]['overflow']) && $this->table[1][1]['overflow'] == 'visible') {
				if ($this->kwt || $this->table_rotate || $this->table_keep_together || $this->ColActive) {
					$this->kwt = false;
					$this->table_rotate = 0;
					$this->table_keep_together = false;
					//$this->Error("mPDF Warning: You cannot use CSS overflow:visible together with any of these functions: 'Keep-with-table', rotated tables, page-break-inside:avoid, or columns");
				}
				$this->_tableColumnWidth($this->table[1][1], true);
				$this->_tableWidth($this->table[1][1]);
			} else {
				if (!$this->kwt_saved) {
					$this->kwt_height = 0;
				}

				list($check, $tablemiw) = $this->_tableColumnWidth($this->table[1][1], true);
				$save_table = $this->table;
				$reset_to_minimum_width = false;
				$added_page = false;

				if ($check > 1) {
					if ($check > $this->shrink_this_table_to_fit && $this->table_rotate) {
						if ($this->y != $this->tMargin) {
							$this->AddPage($this->CurOrientation);
							$this->kwt_moved = true;
						}
						$added_page = true;
						$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
						//$check = $tablemiw/$this->tbrot_maxw; 	// undo any shrink
						$check = 1;  // undo any shrink
					}
					$reset_to_minimum_width = true;
				}

				if ($reset_to_minimum_width) {
					$this->shrin_k = $check;

					$this->default_font_size /= $this->shrin_k;
					$this->SetFontSize($this->default_font_size, false);

					$this->shrinkTable($this->table[1][1], $this->shrin_k);

					$this->_tableColumnWidth($this->table[1][1], false); // repeat
					// Starting at $this->innermostTableLevel
					// Shrink table values - and redo columnWidth
					for ($lvl = 2; $lvl <= $this->innermostTableLevel; $lvl++) {
						for ($nid = 1; $nid <= $this->tbctr[$lvl]; $nid++) {
							$this->shrinkTable($this->table[$lvl][$nid], $this->shrin_k);
							$this->_tableColumnWidth($this->table[$lvl][$nid], false);
						}
					}
				}

				// Set table cell widths for top level table
				// Use $shrin_k to resize but don't change again
				$this->SetLineHeight('', $this->table[1][1]['cellLineHeight']);

				// Top level table
				$this->_tableWidth($this->table[1][1]);
			}

			// Now work through any nested tables setting child table[w'] = parent cell['w']
			// Now do nested tables _tableWidth
			for ($lvl = 2; $lvl <= $this->innermostTableLevel; $lvl++) {
				for ($nid = 1; $nid <= $this->tbctr[$lvl]; $nid++) {
					// HERE set child table width = cell width

					list($parentrow, $parentcol, $parentnid) = $this->table[$lvl][$nid]['nestedpos'];

					$c = & $this->table[($lvl - 1)][$parentnid]['cells'][$parentrow][$parentcol];

					if (isset($c['colspan']) && $c['colspan'] > 1) {
						$parentwidth = 0;
						for ($cs = 0; $cs < $c['colspan']; $cs++) {
							$parentwidth += $this->table[($lvl - 1)][$parentnid]['wc'][$parentcol + $cs];
						}
					} else {
						$parentwidth = $this->table[($lvl - 1)][$parentnid]['wc'][$parentcol];
					}


					//$parentwidth -= ALLOW FOR PADDING ETC.in parent cell
					if (!$this->simpleTables) {
						if ($this->packTableData) {
							list($bt, $br, $bb, $bl) = $this->_getBorderWidths($c['borderbin']);
						} else {
							$br = $c['border_details']['R']['w'];
							$bl = $c['border_details']['L']['w'];
						}
						if ($this->table[$lvl - 1][$parentnid]['borders_separate']) {
							$parentwidth -= $br + $bl + $c['padding']['L'] + $c['padding']['R'] + $this->table[($lvl - 1)][$parentnid]['border_spacing_H'];
						} else {
							$parentwidth -= $br / 2 + $bl / 2 + $c['padding']['L'] + $c['padding']['R'];
						}
					} else if ($this->simpleTables) {
						if ($this->table[$lvl - 1][$parentnid]['borders_separate']) {
							$parentwidth -= $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['L']['w'] + $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['R']['w'] + $c['padding']['L'] + $c['padding']['R'] + $this->table[($lvl - 1)][$parentnid]['border_spacing_H'];
						} else {
							$parentwidth -= $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['L']['w'] / 2 + $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['R']['w'] / 2 + $c['padding']['L'] + $c['padding']['R'];
						}
					}
					if (isset($this->table[$lvl][$nid]['wpercent']) && $this->table[$lvl][$nid]['wpercent'] && $lvl > 1) {
						$this->table[$lvl][$nid]['w'] = $parentwidth;
					} else if ($parentwidth > $this->table[$lvl][$nid]['maw']) {
						$this->table[$lvl][$nid]['w'] = $this->table[$lvl][$nid]['maw'];
					} else {
						$this->table[$lvl][$nid]['w'] = $parentwidth;
					}
					unset($c);
					$this->_tableWidth($this->table[$lvl][$nid]);
				}
			}

			// Starting at $this->innermostTableLevel
			// Cascade back up nested tables: setting heights back up the tree
			for ($lvl = $this->innermostTableLevel; $lvl > 0; $lvl--) {
				for ($nid = 1; $nid <= $this->tbctr[$lvl]; $nid++) {
					list($tableheight, $maxrowheight, $fullpage, $remainingpage, $maxfirstrowheight) = $this->_tableHeight($this->table[$lvl][$nid]);
				}
			}
			if ($this->progressBar) {
				$this->UpdateProgressBar(7, 20, ' ');
			} // *PROGRESS-BAR*
			if ($this->table[1][1]['overflow'] == 'visible') {
				if ($maxrowheight > $fullpage) {
					$this->Error("mPDF Warning: A Table row is greater than available height. You cannot use CSS overflow:visible");
				}
				if ($maxfirstrowheight > $remainingpage) {
					$this->AddPage($this->CurOrientation);
				}
				$r = 0;
				$c = 0;
				$p = 0;
				$y = 0;
				$finished = false;
				while (!$finished) {
					list($finished, $r, $c, $p, $y, $y0) = $this->_tableWrite($this->table[1][1], true, $r, $c, $p, $y);
					if (!$finished) {
						$this->AddPage($this->CurOrientation);
						// If printed something on first spread, set same y
						if ($r == 0 && $y0 > -1) {
							$this->y = $y0;
						}
					}
				}
			} else {
				$recalculate = 1;
				$forcerecalc = false;
				// RESIZING ALGORITHM
				if ($maxrowheight > $fullpage) {
					$recalculate = $this->tbsqrt($maxrowheight / $fullpage, 1);
					$forcerecalc = true;
				} else if ($this->table_rotate) { // NB $remainingpage == $fullpage == the width of the page
					if ($tableheight > $remainingpage) {
						// If can fit on remainder of page whilst respecting autsize value..
						if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, 1)) <= $this->shrink_this_table_to_fit) {
							$recalculate = $this->tbsqrt($tableheight / $remainingpage, 1);
						} else if (!$added_page) {
							if ($this->y != $this->tMargin) {
								$this->AddPage($this->CurOrientation);
								$this->kwt_moved = true;
							}
							$added_page = true;
							$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
							// 0.001 to force it to recalculate
							$recalculate = (1 / $this->shrin_k) + 0.001;  // undo any shrink
						}
					} else {
						$recalculate = 1;
					}
				} else if ($this->table_keep_together || ($this->table[1][1]['nr'] == 1 && !$this->writingHTMLfooter)) {
					if ($tableheight > $fullpage) {
						if (($this->shrin_k * $this->tbsqrt($tableheight / $fullpage, 1)) <= $this->shrink_this_table_to_fit) {
							$recalculate = $this->tbsqrt($tableheight / $fullpage, 1);
						} else if ($this->tableMinSizePriority) {
							$this->table_keep_together = false;
							$recalculate = 1.001;
						} else {
							if ($this->y != $this->tMargin) {
								$this->AddPage($this->CurOrientation);
								$this->kwt_moved = true;
							}
							$added_page = true;
							$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
							$recalculate = $this->tbsqrt($tableheight / $fullpage, 1);
						}
					} else if ($tableheight > $remainingpage) {
						// If can fit on remainder of page whilst respecting autsize value..
						if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, 1)) <= $this->shrink_this_table_to_fit) {
							$recalculate = $this->tbsqrt($tableheight / $remainingpage, 1);
						} else {
							if ($this->y != $this->tMargin) {
								// mPDF 6
								if ($this->AcceptPageBreak()) {
									$this->AddPage($this->CurOrientation);
								} else if ($this->ColActive && $tableheight > (($this->h - $this->bMargin) - $this->y0)) {
									$this->AddPage($this->CurOrientation);
								}
								$this->kwt_moved = true;
							}
							$added_page = true;
							$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
							$recalculate = 1.001;
						}
					} else {
						$recalculate = 1;
					}
				} else {
					$recalculate = 1;
				}

				if ($recalculate > $this->shrink_this_table_to_fit && !$forcerecalc) {
					$recalculate = $this->shrink_this_table_to_fit;
				}

				$iteration = 1;

				// RECALCULATE
				while ($recalculate <> 1) {
					$this->shrin_k1 = $recalculate;
					$this->shrin_k *= $recalculate;
					$this->default_font_size /= ($this->shrin_k1);
					$this->SetFontSize($this->default_font_size, false);
					$this->SetLineHeight('', $this->table[1][1]['cellLineHeight']);
					$this->table = $save_table;
					if ($this->shrin_k <> 1) {
						$this->shrinkTable($this->table[1][1], $this->shrin_k);
					}
					$this->_tableColumnWidth($this->table[1][1], false); // repeat
					// Starting at $this->innermostTableLevel
					// Shrink table values - and redo columnWidth
					for ($lvl = 2; $lvl <= $this->innermostTableLevel; $lvl++) {
						for ($nid = 1; $nid <= $this->tbctr[$lvl]; $nid++) {
							if ($this->shrin_k <> 1) {
								$this->shrinkTable($this->table[$lvl][$nid], $this->shrin_k);
							}
							$this->_tableColumnWidth($this->table[$lvl][$nid], false);
						}
					}
					// Set table cell widths for top level table
					// Top level table
					$this->_tableWidth($this->table[1][1]);

					// Now work through any nested tables setting child table[w'] = parent cell['w']
					// Now do nested tables _tableWidth
					for ($lvl = 2; $lvl <= $this->innermostTableLevel; $lvl++) {
						for ($nid = 1; $nid <= $this->tbctr[$lvl]; $nid++) {
							// HERE set child table width = cell width

							list($parentrow, $parentcol, $parentnid) = $this->table[$lvl][$nid]['nestedpos'];
							$c = & $this->table[($lvl - 1)][$parentnid]['cells'][$parentrow][$parentcol];

							if (isset($c['colspan']) && $c['colspan'] > 1) {
								$parentwidth = 0;
								for ($cs = 0; $cs < $c['colspan']; $cs++) {
									$parentwidth += $this->table[($lvl - 1)][$parentnid]['wc'][$parentcol + $cs];
								}
							} else {
								$parentwidth = $this->table[($lvl - 1)][$parentnid]['wc'][$parentcol];
							}

							//$parentwidth -= ALLOW FOR PADDING ETC.in parent cell
							if (!$this->simpleTables) {
								if ($this->packTableData) {
									list($bt, $br, $bb, $bl) = $this->_getBorderWidths($c['borderbin']);
								} else {
									$br = $c['border_details']['R']['w'];
									$bl = $c['border_details']['L']['w'];
								}
								if ($this->table[$lvl - 1][$parentnid]['borders_separate']) {
									$parentwidth -= $br + $bl + $c['padding']['L'] + $c['padding']['R'] + $this->table[($lvl - 1)][$parentnid]['border_spacing_H'];
								} else {
									$parentwidth -= $br / 2 + $bl / 2 + $c['padding']['L'] + $c['padding']['R'];
								}
							} else if ($this->simpleTables) {
								if ($this->table[$lvl - 1][$parentnid]['borders_separate']) {
									$parentwidth -= $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['L']['w'] + $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['R']['w'] + $c['padding']['L'] + $c['padding']['R'] + $this->table[($lvl - 1)][$parentnid]['border_spacing_H'];
								} else {
									$parentwidth -= ($this->table[($lvl - 1)][$parentnid]['simple']['border_details']['L']['w'] + $this->table[($lvl - 1)][$parentnid]['simple']['border_details']['R']['w']) / 2 + $c['padding']['L'] + $c['padding']['R'];
								}
							}
							if (isset($this->table[$lvl][$nid]['wpercent']) && $this->table[$lvl][$nid]['wpercent'] && $lvl > 1) {
								$this->table[$lvl][$nid]['w'] = $parentwidth;
							} else if ($parentwidth > $this->table[$lvl][$nid]['maw']) {
								$this->table[$lvl][$nid]['w'] = $this->table[$lvl][$nid]['maw'];
							} else {
								$this->table[$lvl][$nid]['w'] = $parentwidth;
							}
							unset($c);
							$this->_tableWidth($this->table[$lvl][$nid]);
						}
					}

					// Starting at $this->innermostTableLevel
					// Cascade back up nested tables: setting heights back up the tree
					for ($lvl = $this->innermostTableLevel; $lvl > 0; $lvl--) {
						for ($nid = 1; $nid <= $this->tbctr[$lvl]; $nid++) {
							list($tableheight, $maxrowheight, $fullpage, $remainingpage, $maxfirstrowheight) = $this->_tableHeight($this->table[$lvl][$nid]);
						}
					}

					// RESIZING ALGORITHM

					if ($maxrowheight > $fullpage) {
						$recalculate = $this->tbsqrt($maxrowheight / $fullpage, $iteration);
						$iteration++;
					} else if ($this->table_rotate && $tableheight > $remainingpage && !$added_page) {
						// If can fit on remainder of page whilst respecting autosize value..
						if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, $iteration)) <= $this->shrink_this_table_to_fit) {
							$recalculate = $this->tbsqrt($tableheight / $remainingpage, $iteration);
							$iteration++;
						} else {
							if (!$added_page) {
								$this->AddPage($this->CurOrientation);
								$added_page = true;
								$this->kwt_moved = true;
								$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
							}
							// 0.001 to force it to recalculate
							$recalculate = (1 / $this->shrin_k) + 0.001;  // undo any shrink
						}
					} else if ($this->table_keep_together || ($this->table[1][1]['nr'] == 1 && !$this->writingHTMLfooter)) {
						if ($tableheight > $fullpage) {
							if (($this->shrin_k * $this->tbsqrt($tableheight / $fullpage, $iteration)) <= $this->shrink_this_table_to_fit) {
								$recalculate = $this->tbsqrt($tableheight / $fullpage, $iteration);
								$iteration++;
							} else if ($this->tableMinSizePriority) {
								$this->table_keep_together = false;
								$recalculate = (1 / $this->shrin_k) + 0.001;
							} else {
								if (!$added_page && $this->y != $this->tMargin) {
									$this->AddPage($this->CurOrientation);
									$added_page = true;
									$this->kwt_moved = true;
									$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
								}
								$recalculate = $this->tbsqrt($tableheight / $fullpage, $iteration);
								$iteration++;
							}
						} else if ($tableheight > $remainingpage) {
							// If can fit on remainder of page whilst respecting autosize value..
							if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, $iteration)) <= $this->shrink_this_table_to_fit) {
								$recalculate = $this->tbsqrt($tableheight / $remainingpage, $iteration);
								$iteration++;
							} else {
								if (!$added_page) {
									// mPDF 6
									if ($this->AcceptPageBreak()) {
										$this->AddPage($this->CurOrientation);
									} else if ($this->ColActive && $tableheight > (($this->h - $this->bMargin) - $this->y0)) {
										$this->AddPage($this->CurOrientation);
									}
									$added_page = true;
									$this->kwt_moved = true;
									$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
								}

								//$recalculate = $this->tbsqrt($tableheight / $fullpage, $iteration); $iteration++;
								$recalculate = (1 / $this->shrin_k) + 0.001;  // undo any shrink
							}
						} else {
							$recalculate = 1;
						}
					} else {
						$recalculate = 1;
					}
				}


				if ($maxfirstrowheight > $remainingpage && !$added_page && !$this->table_rotate && !$this->ColActive && !$this->table_keep_together && !$this->writingHTMLheader && !$this->writingHTMLfooter) {
					$this->AddPage($this->CurOrientation);
					$this->kwt_moved = true;
				}

				// keep-with-table: if page has advanced, print out buffer now, else done in fn. _Tablewrite()
				if ($this->kwt_saved && $this->kwt_moved) {
					$this->printkwtbuffer();
					$this->kwt_moved = false;
					$this->kwt_saved = false;
				}

				if ($this->progressBar) {
					$this->UpdateProgressBar(7, 30, ' ');
				} // *PROGRESS-BAR*
				// Recursively writes all tables starting at top level
				$this->_tableWrite($this->table[1][1]);

				if ($this->table_rotate && $this->tablebuffer) {
					$this->PageBreakTrigger = $this->h - $this->bMargin;
					$save_tr = $this->table_rotate;
					$save_y = $this->y;
					$this->table_rotate = 0;
					$this->y = $this->tbrot_y0;
					$h = $this->tbrot_w;
					$this->DivLn($h, $this->blklvl, true);

					$this->table_rotate = $save_tr;
					$this->y = $save_y;

					$this->printtablebuffer();
				}
				$this->table_rotate = 0;
			}


			$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];

			$this->maxPosR = max($this->maxPosR, ($this->x + $this->table[1][1]['w']));

			$this->blockjustfinished = true;
			$this->lastblockbottommargin = $this->table[1][1]['margin']['B'];
			//Reset values

			if (isset($this->table[1][1]['page_break_after'])) {
				$page_break_after = $this->table[1][1]['page_break_after'];
			} else {
				$page_break_after = '';
			}

			// Keep-with-table
			$this->kwt = false;
			$this->kwt_y0 = 0;
			$this->kwt_x0 = 0;
			$this->kwt_height = 0;
			$this->kwt_buffer = array();
			$this->kwt_Links = array();
			$this->kwt_Annots = array();
			$this->kwt_moved = false;
			$this->kwt_saved = false;

			$this->kwt_Reference = array();
			$this->kwt_BMoutlines = array();
			$this->kwt_toc = array();

			$this->shrin_k = 1;
			$this->shrink_this_table_to_fit = 0;

			unset($this->table);
			$this->table = array(); //array
			$this->tableLevel = 0;
			$this->tbctr = array();
			$this->innermostTableLevel = 0;
			$this->cssmgr->tbCSSlvl = 0;
			$this->cssmgr->tablecascadeCSS = array();

			unset($this->cell);
			$this->cell = array(); //array

			$this->col = -1; //int
			$this->row = -1; //int
			$this->Reset();

			$this->cellPaddingL = 0;
			$this->cellPaddingT = 0;
			$this->cellPaddingR = 0;
			$this->cellPaddingB = 0;
			$this->cMarginL = 0;
			$this->cMarginT = 0;
			$this->cMarginR = 0;
			$this->cMarginB = 0;
			$this->default_font_size = $this->original_default_font_size;
			$this->default_font = $this->original_default_font;
			$this->SetFontSize($this->default_font_size, false);
			$this->SetFont($this->default_font, '', 0, false);
			$this->SetLineHeight();
			if (isset($this->blk[$this->blklvl]['InlineProperties'])) {
				$this->restoreInlineProperties($this->blk[$this->blklvl]['InlineProperties']);
			}
			if ($this->progressBar) {
				$this->UpdateProgressBar(7, 100, ' ');
			} // *PROGRESS-BAR*

			if ($page_break_after) {
				$save_blklvl = $this->blklvl;
				$save_blk = $this->blk;
				$save_silp = $this->saveInlineProperties();
				$save_ilp = $this->InlineProperties;
				$save_bflp = $this->InlineBDF;
				$save_bflpc = $this->InlineBDFctr; // mPDF 6
				// mPDF 6 pagebreaktype
				$startpage = $this->page;
				$pagebreaktype = $this->defaultPagebreakType;
				if ($this->ColActive) {
					$pagebreaktype = 'cloneall';
				}

				// mPDF 6 pagebreaktype
				$this->_preForcedPagebreak($pagebreaktype);

				if ($page_break_after == 'RIGHT') {
					$this->AddPage($this->CurOrientation, 'NEXT-ODD', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
				} else if ($page_break_after == 'LEFT') {
					$this->AddPage($this->CurOrientation, 'NEXT-EVEN', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
				} else {
					$this->AddPage($this->CurOrientation, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
				}

				// mPDF 6 pagebreaktype
				$this->_postForcedPagebreak($pagebreaktype, $startpage, $save_blk, $save_blklvl);

				$this->InlineProperties = $save_ilp;
				$this->InlineBDF = $save_bflp;
				$this->InlineBDFctr = $save_bflpc; // mPDF 6
				$this->restoreInlineProperties($save_silp);
			}
		}
		/* -- END TABLES -- */
	}

	/* -- TABLES -- */

// This function determines the shrink factor when resizing tables
// val is the table_height / page_height_available
// returns a scaling factor used as $shrin_k to resize the table
// Overcompensating will be quicker but may unnecessarily shrink table too much
// Undercompensating means it will reiterate more times (taking more processing time)
	function tbsqrt($val, $iteration = 3)
	{
		$k = 4; // Alters number of iterations until it returns $val itself - Must be > 2
		// Probably best guess and most accurate
		if ($iteration == 1)
			return sqrt($val);
		// Faster than using sqrt (because it won't undercompensate), and gives reasonable results
		//return 1+(($val-1)/2);
		$x = 2 - (($iteration - 2) / ($k - 2));
		if ($x == 0) {
			$ret = $val + 0.00001;
		} else if ($x < 0) {
			$ret = 1 + ( pow(2, ($iteration - 2 - $k)) / 1000 );
		} else {
			$ret = 1 + (($val - 1) / $x);
		}
		return $ret;
	}

	/* -- END TABLES -- */

	function _saveTextBuffer($t, $link = '', $intlink = '', $return = false)
	{ // mPDF 6  Lists
		$arr = array();
		$arr[0] = $t;
		if (isset($link) && $link)
			$arr[1] = $link;
		$arr[2] = $this->currentfontstyle;
		if (isset($this->colorarray) && $this->colorarray)
			$arr[3] = $this->colorarray;
		$arr[4] = $this->currentfontfamily;
		$arr[5] = $this->currentLang; // mPDF 6
		if (isset($intlink) && $intlink)
			$arr[7] = $intlink;
		// mPDF 6
		// If Kerning set for OTL, and useOTL has positive value, but has not set for this particular script,
		// set for kerning via kern table
		// e.g. Latin script when useOTL set as 0x80
		if (isset($this->OTLtags['Plus']) && strpos($this->OTLtags['Plus'], 'kern') !== false && empty($this->OTLdata['GPOSinfo'])) {
			$this->textvar = ($this->textvar | FC_KERNING);
		}
		$arr[8] = $this->textvar; // mPDF 5.7.1
		if (isset($this->textparam) && $this->textparam)
			$arr[9] = $this->textparam;
		if (isset($this->spanbgcolorarray) && $this->spanbgcolorarray)
			$arr[10] = $this->spanbgcolorarray;
		$arr[11] = $this->currentfontsize;
		if (isset($this->ReqFontStyle) && $this->ReqFontStyle)
			$arr[12] = $this->ReqFontStyle;
		if (isset($this->lSpacingCSS) && $this->lSpacingCSS)
			$arr[14] = $this->lSpacingCSS;
		if (isset($this->wSpacingCSS) && $this->wSpacingCSS)
			$arr[15] = $this->wSpacingCSS;
		if (isset($this->spanborddet) && $this->spanborddet)
			$arr[16] = $this->spanborddet;
		if (isset($this->textshadow) && $this->textshadow)
			$arr[17] = $this->textshadow;
		if (isset($this->OTLdata) && $this->OTLdata) {
			$arr[18] = $this->OTLdata;
			$this->OTLdata = array();
		} // mPDF 5.7.1
		else {
			$arr[18] = NULL;
		}
		// mPDF 6  Lists
		if ($return) {
			return ($arr);
		}
		if ($this->listitem) {
			$this->textbuffer[] = $this->listitem;
			$this->listitem = array();
		}
		$this->textbuffer[] = $arr;
	}

	function _saveCellTextBuffer($t, $link = '', $intlink = '')
	{
		$arr = array();
		$arr[0] = $t;
		if (isset($link) && $link)
			$arr[1] = $link;
		$arr[2] = $this->currentfontstyle;
		if (isset($this->colorarray) && $this->colorarray)
			$arr[3] = $this->colorarray;
		$arr[4] = $this->currentfontfamily;
		if (isset($intlink) && $intlink)
			$arr[7] = $intlink;
		// mPDF 6
		// If Kerning set for OTL, and useOTL has positive value, but has not set for this particular script,
		// set for kerning via kern table
		// e.g. Latin script when useOTL set as 0x80
		if (isset($this->OTLtags['Plus']) && strpos($this->OTLtags['Plus'], 'kern') !== false && empty($this->OTLdata['GPOSinfo'])) {
			$this->textvar = ($this->textvar | FC_KERNING);
		}
		$arr[8] = $this->textvar; // mPDF 5.7.1
		if (isset($this->textparam) && $this->textparam)
			$arr[9] = $this->textparam;
		if (isset($this->spanbgcolorarray) && $this->spanbgcolorarray)
			$arr[10] = $this->spanbgcolorarray;
		$arr[11] = $this->currentfontsize;
		if (isset($this->ReqFontStyle) && $this->ReqFontStyle)
			$arr[12] = $this->ReqFontStyle;
		if (isset($this->lSpacingCSS) && $this->lSpacingCSS)
			$arr[14] = $this->lSpacingCSS;
		if (isset($this->wSpacingCSS) && $this->wSpacingCSS)
			$arr[15] = $this->wSpacingCSS;
		if (isset($this->spanborddet) && $this->spanborddet)
			$arr[16] = $this->spanborddet;
		if (isset($this->textshadow) && $this->textshadow)
			$arr[17] = $this->textshadow;
		if (isset($this->OTLdata) && $this->OTLdata) {
			$arr[18] = $this->OTLdata;
			$this->OTLdata = array();
		} // mPDF 5.7.1
		else {
			$arr[18] = NULL;
		}
		$this->cell[$this->row][$this->col]['textbuffer'][] = $arr;
	}

	function printbuffer($arrayaux, $blockstate = 0, $is_table = false, $table_draft = false, $cell_dir = '')
	{
// $blockstate = 0;	// NO margins/padding
// $blockstate = 1;	// Top margins/padding only
// $blockstate = 2;	// Bottom margins/padding only
// $blockstate = 3;	// Top & bottom margins/padding
		$this->spanbgcolorarray = '';
		$this->spanbgcolor = false;
		$this->spanborder = false;
		$this->spanborddet = array();
		$paint_ht_corr = 0;
		/* -- CSS-FLOAT -- */
		if (count($this->floatDivs)) {
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
			if (($this->blk[$this->blklvl]['inner_width'] - $l_width - $r_width) < (2 * $this->GetCharWidth('W', false))) {
				// Too narrow to fit - try to move down past L or R float
				if ($l_max < $r_max && ($this->blk[$this->blklvl]['inner_width'] - $r_width) > (2 * $this->GetCharWidth('W', false))) {
					$this->ClearFloats('LEFT', $this->blklvl);
				} else if ($r_max < $l_max && ($this->blk[$this->blklvl]['inner_width'] - $l_width) > (2 * $this->GetCharWidth('W', false))) {
					$this->ClearFloats('RIGHT', $this->blklvl);
				} else {
					$this->ClearFloats('BOTH', $this->blklvl);
				}
			}
		}
		/* -- END CSS-FLOAT -- */
		$bak_y = $this->y;
		$bak_x = $this->x;
		$align = '';
		if (!$is_table) {
			if (isset($this->blk[$this->blklvl]['align']) && $this->blk[$this->blklvl]['align']) {
				$align = $this->blk[$this->blklvl]['align'];
			}
			// Block-align is set by e.g. <.. align="center"> Takes priority for this block but not inherited
			if (isset($this->blk[$this->blklvl]['block-align']) && $this->blk[$this->blklvl]['block-align']) {
				$align = $this->blk[$this->blklvl]['block-align'];
			}
			if (isset($this->blk[$this->blklvl]['direction']))
				$blockdir = $this->blk[$this->blklvl]['direction'];
			else
				$blockdir = "";
			$this->divwidth = $this->blk[$this->blklvl]['width'];
		}
		else {
			$align = $this->cellTextAlign;
			$blockdir = $cell_dir;
		}
		$oldpage = $this->page;

		// ADDED for Out of Block now done as Flowing Block
		if ($this->divwidth == 0) {
			$this->divwidth = $this->pgwidth;
		}

		if (!$is_table) {
			$this->SetLineHeight($this->FontSizePt, $this->blk[$this->blklvl]['line_height']);
		}
		$this->divheight = $this->lineheight;
		$old_height = $this->divheight;

		// As a failsafe - if font has been set but not output to page
		if (!$table_draft)
			$this->SetFont($this->default_font, '', $this->default_font_size, true, true); // force output to page

		$this->newFlowingBlock($this->divwidth, $this->divheight, $align, $is_table, $blockstate, true, $blockdir, $table_draft);

		$array_size = count($arrayaux);

		// Added - Otherwise <div><div><p> did not output top margins/padding for 1st/2nd div
		if ($array_size == 0) {
			$this->finishFlowingBlock(true);
		} // true = END of flowing block
		// mPDF 6
		// ALL the chunks of textbuffer need to have at least basic OTLdata set
		// First make sure each element/chunk has the OTLdata for Bidi set.
		for ($i = 0; $i < $array_size; $i++) {
			if (empty($arrayaux[$i][18])) {
				if (substr($arrayaux[$i][0], 0, 3) == "\xbb\xa4\xac") { // object identifier has been identified!
					$unicode = array(0xFFFC); // Object replacement character
				} else {
					$unicode = $this->UTF8StringToArray($arrayaux[$i][0], false);
				}
				$is_strong = false;
				$this->getBasicOTLdata($arrayaux[$i][18], $unicode, $is_strong);
			}
			// Gets messed up if try and use core fonts inside a paragraph of text which needs to be BiDi re-ordered or OTLdata set
			if (($blockdir == 'rtl' || $this->biDirectional) && isset($arrayaux[$i][4]) && in_array($arrayaux[$i][4], array('ccourier', 'ctimes', 'chelvetica', 'csymbol', 'czapfdingbats'))) {
				$this->Error("You cannot use core fonts in a document which contains RTL text.");
			}
		}
		// mPDF 6
		// Process bidirectional text ready for bidi-re-ordering (which is done after line-breaks are established in WriteFlowingBlock etc.)
		if (($blockdir == 'rtl' || $this->biDirectional) && !$table_draft) {
			if (!class_exists('otl', false)) {
				include(_MPDF_PATH . 'classes/otl.php');
			}
			if (empty($this->otl)) {
				$this->otl = new otl($this);
			}
			$this->otl->_bidiPrepare($arrayaux, $blockdir);
			$array_size = count($arrayaux);
		}


		// Remove empty items // mPDF 6
		for ($i = $array_size - 1; $i > 0; $i--) {
			if (empty($arrayaux[$i][0]) && $arrayaux[$i][16] !== '0' && empty($arrayaux[$i][7])) {
				unset($arrayaux[$i]);
			}
		}

		// Correct adjoining borders for inline elements
		if (isset($arrayaux[0][16])) {
			$lastspanborder = $arrayaux[0][16];
		} else {
			$lastspanborder = false;
		}
		for ($i = 1; $i < $array_size; $i++) {
			if (isset($arrayaux[$i][16]) && $arrayaux[$i][16] == $lastspanborder &&
				((!isset($arrayaux[$i][9]['bord-decoration']) && !isset($arrayaux[$i - 1][9]['bord-decoration'])) ||
				(isset($arrayaux[$i][9]['bord-decoration']) && isset($arrayaux[$i - 1][9]['bord-decoration']) && $arrayaux[$i][9]['bord-decoration'] == $arrayaux[$i - 1][9]['bord-decoration'])
				)
			) {
				if (isset($arrayaux[$i][16]['R'])) {
					$lastspanborder = $arrayaux[$i][16];
				} else {
					$lastspanborder = false;
				}
				$arrayaux[$i][16]['L']['s'] = 0;
				$arrayaux[$i][16]['L']['w'] = 0;
				$arrayaux[$i - 1][16]['R']['s'] = 0;
				$arrayaux[$i - 1][16]['R']['w'] = 0;
			} else {
				if (isset($arrayaux[$i][16]['R'])) {
					$lastspanborder = $arrayaux[$i][16];
				} else {
					$lastspanborder = false;
				}
			}
		}

		for ($i = 0; $i < $array_size; $i++) {
			// COLS
			$oldcolumn = $this->CurrCol;
			$vetor = $arrayaux[$i];
			if ($i == 0 and $vetor[0] != "\n" and ! $this->ispre) {
				$vetor[0] = ltrim($vetor[0]);
				if (!empty($vetor[18])) {
					$this->otl->trimOTLdata($vetor[18], true, false);
				} // *OTL*
			}

			// FIXED TO ALLOW IT TO SHOW '0'
			if (empty($vetor[0]) && !($vetor[0] === '0') && empty($vetor[7])) { //Ignore empty text and not carrying an internal link
				//Check if it is the last element. If so then finish printing the block
				if ($i == ($array_size - 1)) {
					$this->finishFlowingBlock(true);
				} // true = END of flowing block
				continue;
			}


			//Activating buffer properties
			if (isset($vetor[11]) and $vetor[11] != '') {   // Font Size
				if ($is_table && $this->shrin_k) {
					$this->SetFontSize($vetor[11] / $this->shrin_k, false);
				} else {
					$this->SetFontSize($vetor[11], false);
				}
			}

			if (isset($vetor[17]) && !empty($vetor[17])) { //TextShadow
				$this->textshadow = $vetor[17];
			}
			if (isset($vetor[16]) && !empty($vetor[16])) { //Border
				$this->spanborddet = $vetor[16];
				$this->spanborder = true;
			}

			if (isset($vetor[15])) {   // Word spacing
				$this->wSpacingCSS = $vetor[15];
				if ($this->wSpacingCSS && strtoupper($this->wSpacingCSS) != 'NORMAL') {
					$this->minwSpacing = $this->ConvertSize($this->wSpacingCSS, $this->FontSize) / $this->shrin_k; // mPDF 5.7.3
				}
			}
			if (isset($vetor[14])) {   // Letter spacing
				$this->lSpacingCSS = $vetor[14];
				if (($this->lSpacingCSS || $this->lSpacingCSS === '0') && strtoupper($this->lSpacingCSS) != 'NORMAL') {
					$this->fixedlSpacing = $this->ConvertSize($this->lSpacingCSS, $this->FontSize) / $this->shrin_k; // mPDF 5.7.3
				}
			}


			if (isset($vetor[10]) and ! empty($vetor[10])) { //Background color
				$this->spanbgcolorarray = $vetor[10];
				$this->spanbgcolor = true;
			}
			if (isset($vetor[9]) and ! empty($vetor[9])) { // Text parameters - Outline + hyphens
				$this->textparam = $vetor[9];
				$this->SetTextOutline($this->textparam);
				// mPDF 5.7.3  inline text-decoration parameters
				if ($is_table && $this->shrin_k) {
					if (isset($this->textparam['text-baseline'])) {
						$this->textparam['text-baseline'] /= $this->shrin_k;
					}
					if (isset($this->textparam['decoration-baseline'])) {
						$this->textparam['decoration-baseline'] /= $this->shrin_k;
					}
					if (isset($this->textparam['decoration-fontsize'])) {
						$this->textparam['decoration-fontsize'] /= $this->shrin_k;
					}
				}
			}
			if (isset($vetor[8])) {  // mPDF 5.7.1
				$this->textvar = $vetor[8];
			}
			if (isset($vetor[7]) and $vetor[7] != '') { // internal target: <a name="anyvalue">
				$ily = $this->y;
				if ($this->table_rotate) {
					$this->internallink[$vetor[7]] = array("Y" => $ily, "PAGE" => $this->page, "tbrot" => true);
				} else if ($this->kwt) {
					$this->internallink[$vetor[7]] = array("Y" => $ily, "PAGE" => $this->page, "kwt" => true);
				} else if ($this->ColActive) {
					$this->internallink[$vetor[7]] = array("Y" => $ily, "PAGE" => $this->page, "col" => $this->CurrCol);
				} else if (!$this->keep_block_together) {
					$this->internallink[$vetor[7]] = array("Y" => $ily, "PAGE" => $this->page);
				}
				if (empty($vetor[0])) { //Ignore empty text
					//Check if it is the last element. If so then finish printing the block
					if ($i == ($array_size - 1)) {
						$this->finishFlowingBlock(true);
					} // true = END of flowing block
					continue;
				}
			}
			if (isset($vetor[5]) and $vetor[5] != '') {  // Language	// mPDF 6
				$this->currentLang = $vetor[5];
			}
			if (isset($vetor[4]) and $vetor[4] != '') {  // Font Family
				$font = $this->SetFont($vetor[4], $this->FontStyle, 0, false);
			}
			if (!empty($vetor[3])) { //Font Color
				$cor = $vetor[3];
				$this->SetTColor($cor);
			}
			if (isset($vetor[2]) and $vetor[2] != '') { //Bold,Italic styles
				$this->SetStyles($vetor[2]);
			}

			if (isset($vetor[12]) and $vetor[12] != '') { //Requested Bold,Italic
				$this->ReqFontStyle = $vetor[12];
			}
			if (isset($vetor[1]) and $vetor[1] != '') { //LINK
				if (strpos($vetor[1], ".") === false && strpos($vetor[1], "@") !== 0) { //assuming every external link has a dot indicating extension (e.g: .html .txt .zip www.somewhere.com etc.)
					//Repeated reference to same anchor?
					while (array_key_exists($vetor[1], $this->internallink))
						$vetor[1] = "#" . $vetor[1];
					$this->internallink[$vetor[1]] = $this->AddLink();
					$vetor[1] = $this->internallink[$vetor[1]];
				}
				$this->HREF = $vetor[1];     // HREF link style set here ******
			}

			// SPECIAL CONTENT - IMAGES & FORM OBJECTS
			//Print-out special content

			if (substr($vetor[0], 0, 3) == "\xbb\xa4\xac") { //identifier has been identified!
				$objattr = $this->_getObjAttr($vetor[0]);

				/* -- TABLES -- */
				if ($objattr['type'] == 'nestedtable') {
					if ($objattr['nestedcontent']) {
						$level = $objattr['level'];
						$table = &$this->table[$level][$objattr['table']];

						if ($table_draft) {
							$this->y += $this->table[($level + 1)][$objattr['nestedcontent']]['h']; // nested table height
							$this->finishFlowingBlock(false, 'nestedtable');
						} else {

							$cell = &$table['cells'][$objattr['row']][$objattr['col']];
							$this->finishFlowingBlock(false, 'nestedtable');
							$save_dw = $this->divwidth;
							$save_buffer = $this->cellBorderBuffer;
							$this->cellBorderBuffer = array();
							$ncx = $this->x;
							list($dummyx, $w) = $this->_tableGetWidth($table, $objattr['row'], $objattr['col']);
							$ntw = $this->table[($level + 1)][$objattr['nestedcontent']]['w']; // nested table width
							if (!$this->simpleTables) {
								if ($this->packTableData) {
									list($bt, $br, $bb, $bl) = $this->_getBorderWidths($cell['borderbin']);
								} else {
									$br = $cell['border_details']['R']['w'];
									$bl = $cell['border_details']['L']['w'];
								}
								if ($table['borders_separate']) {
									$innerw = $w - $bl - $br - $cell['padding']['L'] - $cell['padding']['R'] - $table['border_spacing_H'];
								} else {
									$innerw = $w - $bl / 2 - $br / 2 - $cell['padding']['L'] - $cell['padding']['R'];
								}
							} else if ($this->simpleTables) {
								if ($table['borders_separate']) {
									$innerw = $w - $table['simple']['border_details']['L']['w'] - $table['simple']['border_details']['R']['w'] - $cell['padding']['L'] - $cell['padding']['R'] - $table['border_spacing_H'];
								} else {
									$innerw = $w - $table['simple']['border_details']['L']['w'] / 2 - $table['simple']['border_details']['R']['w'] / 2 - $cell['padding']['L'] - $cell['padding']['R'];
								}
							}
							if ($cell['a'] == 'C' || $this->table[($level + 1)][$objattr['nestedcontent']]['a'] == 'C') {
								$ncx += ($innerw - $ntw) / 2;
							} elseif ($cell['a'] == 'R' || $this->table[($level + 1)][$objattr['nestedcontent']]['a'] == 'R') {
								$ncx += $innerw - $ntw;
							}
							$this->x = $ncx;

							$this->_tableWrite($this->table[($level + 1)][$objattr['nestedcontent']]);
							$this->cellBorderBuffer = $save_buffer;
							$this->x = $bak_x;
							$this->divwidth = $save_dw;
						}

						$this->newFlowingBlock($this->divwidth, $this->divheight, $align, $is_table, $blockstate, false, $blockdir, $table_draft);
					}
				} else {
					/* -- END TABLES -- */
					if ($is_table) { // *TABLES*
						$maxWidth = $this->divwidth;  // *TABLES*
					} // *TABLES*
					else { // *TABLES*
						$maxWidth = $this->divwidth - ($this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_right'] + $this->blk[$this->blklvl]['border_right']['w']);
					} // *TABLES*

					/* -- CSS-IMAGE-FLOAT -- */
					// If float (already) exists at this level
					if (isset($this->floatmargins['R']) && $this->y <= $this->floatmargins['R']['y1'] && $this->y >= $this->floatmargins['R']['y0']) {
						$maxWidth -= $this->floatmargins['R']['w'];
					}
					if (isset($this->floatmargins['L']) && $this->y <= $this->floatmargins['L']['y1'] && $this->y >= $this->floatmargins['L']['y0']) {
						$maxWidth -= $this->floatmargins['L']['w'];
					}
					/* -- END CSS-IMAGE-FLOAT -- */

					list($skipln) = $this->inlineObject($objattr['type'], '', $this->y, $objattr, $this->lMargin, ($this->flowingBlockAttr['contentWidth'] / _MPDFK), $maxWidth, $this->flowingBlockAttr['height'], false, $is_table);
					//  1 -> New line needed because of width
					// -1 -> Will fit width on line but NEW PAGE REQUIRED because of height
					// -2 -> Will not fit on line therefore needs new line but thus NEW PAGE REQUIRED
					$iby = $this->y;
					$oldpage = $this->page;
					$oldcol = $this->CurrCol;
					if (($skipln == 1 || $skipln == -2) && !isset($objattr['float'])) {
						$this->finishFlowingBlock(false, $objattr['type']);
						$this->newFlowingBlock($this->divwidth, $this->divheight, $align, $is_table, $blockstate, false, $blockdir, $table_draft);
					}

					if (!$table_draft) {
						$thispage = $this->page;
						if ($this->CurrCol != $oldcol) {
							$changedcol = true;
						} else {
							$changedcol = false;
						}

						// the previous lines can already have triggered page break or column change
						if (!$changedcol && $skipln < 0 && $this->AcceptPageBreak() && $thispage == $oldpage) {

							$this->AddPage($this->CurOrientation);

							// Added to correct Images already set on line before page advanced
							// i.e. if second inline image on line is higher than first and forces new page
							if (count($this->objectbuffer)) {
								$yadj = $iby - $this->y;
								foreach ($this->objectbuffer AS $ib => $val) {
									if ($this->objectbuffer[$ib]['OUTER-Y'])
										$this->objectbuffer[$ib]['OUTER-Y'] -= $yadj;
									if ($this->objectbuffer[$ib]['BORDER-Y'])
										$this->objectbuffer[$ib]['BORDER-Y'] -= $yadj;
									if ($this->objectbuffer[$ib]['INNER-Y'])
										$this->objectbuffer[$ib]['INNER-Y'] -= $yadj;
								}
							}
						}

						// Added to correct for OddEven Margins
						if ($this->page != $oldpage) {
							if (($this->page - $oldpage) % 2 == 1) {
								$bak_x += $this->MarginCorrection;
							}
							$oldpage = $this->page;
							$y = $this->tMargin - $paint_ht_corr;
							$this->oldy = $this->tMargin - $paint_ht_corr;
							$old_height = 0;
						}
						$this->x = $bak_x;
						/* -- COLUMNS -- */
						// COLS
						// OR COLUMN CHANGE
						if ($this->CurrCol != $oldcolumn) {
							if ($this->directionality == 'rtl') { // *OTL*
								$bak_x -= ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap); // *OTL*
							} // *OTL*
							else { // *OTL*
								$bak_x += ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap);
							} // *OTL*
							$this->x = $bak_x;
							$oldcolumn = $this->CurrCol;
							$y = $this->y0 - $paint_ht_corr;
							$this->oldy = $this->y0 - $paint_ht_corr;
							$old_height = 0;
						}
						/* -- END COLUMNS -- */
					}

					/* -- CSS-IMAGE-FLOAT -- */
					if ($objattr['type'] == 'image' && isset($objattr['float'])) {
						$fy = $this->y;

						// DIV TOP MARGIN/BORDER/PADDING
						if ($this->flowingBlockAttr['newblock'] && ($this->flowingBlockAttr['blockstate'] == 1 || $this->flowingBlockAttr['blockstate'] == 3) && $this->flowingBlockAttr['lineCount'] == 0) {
							$fy += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
						}

						if ($objattr['float'] == 'R') {
							$fx = $this->w - $this->rMargin - $objattr['width'] - ($this->blk[$this->blklvl]['outer_right_margin'] + $this->blk[$this->blklvl]['border_right']['w'] + $this->blk[$this->blklvl]['padding_right']);
						} else if ($objattr['float'] == 'L') {
							$fx = $this->lMargin + ($this->blk[$this->blklvl]['outer_left_margin'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_left']);
						}
						$w = $objattr['width'];
						$h = abs($objattr['height']);

						$widthLeft = $maxWidth - ($this->flowingBlockAttr['contentWidth'] / _MPDFK);
						$maxHeight = $this->h - ($this->tMargin + $this->margin_header + $this->bMargin + 10);
						// For Images
						$extraWidth = ($objattr['border_left']['w'] + $objattr['border_right']['w'] + $objattr['margin_left'] + $objattr['margin_right']);
						$extraHeight = ($objattr['border_top']['w'] + $objattr['border_bottom']['w'] + $objattr['margin_top'] + $objattr['margin_bottom']);

						if ($objattr['itype'] == 'wmf' || $objattr['itype'] == 'svg') {
							$file = $objattr['file'];
							$info = $this->formobjects[$file];
						} else {
							$file = $objattr['file'];
							$info = $this->images[$file];
						}
						$img_w = $w - $extraWidth;
						$img_h = $h - $extraHeight;
						if ($objattr['border_left']['w']) {
							$objattr['BORDER-WIDTH'] = $img_w + (($objattr['border_left']['w'] + $objattr['border_right']['w']) / 2);
							$objattr['BORDER-HEIGHT'] = $img_h + (($objattr['border_top']['w'] + $objattr['border_bottom']['w']) / 2);
							$objattr['BORDER-X'] = $fx + $objattr['margin_left'] + (($objattr['border_left']['w']) / 2);
							$objattr['BORDER-Y'] = $fy + $objattr['margin_top'] + (($objattr['border_top']['w']) / 2);
						}
						$objattr['INNER-WIDTH'] = $img_w;
						$objattr['INNER-HEIGHT'] = $img_h;
						$objattr['INNER-X'] = $fx + $objattr['margin_left'] + ($objattr['border_left']['w']);
						$objattr['INNER-Y'] = $fy + $objattr['margin_top'] + ($objattr['border_top']['w']);
						$objattr['ID'] = $info['i'];
						$objattr['OUTER-WIDTH'] = $w;
						$objattr['OUTER-HEIGHT'] = $h;
						$objattr['OUTER-X'] = $fx;
						$objattr['OUTER-Y'] = $fy;
						if ($objattr['float'] == 'R') {
							// If R float already exists at this level
							$this->floatmargins['R']['skipline'] = false;
							if (isset($this->floatmargins['R']['y1']) && $this->floatmargins['R']['y1'] > 0 && $fy < $this->floatmargins['R']['y1']) {
								$this->WriteFlowingBlock($vetor[0], $vetor[18]);  // mPDF 5.7.1
							}
							// If L float already exists at this level
							else if (isset($this->floatmargins['L']['y1']) && $this->floatmargins['L']['y1'] > 0 && $fy < $this->floatmargins['L']['y1']) {
								// Final check distance between floats is not now too narrow to fit text
								$mw = 2 * $this->GetCharWidth('W', false);
								if (($this->blk[$this->blklvl]['inner_width'] - $w - $this->floatmargins['L']['w']) < $mw) {
									$this->WriteFlowingBlock($vetor[0], $vetor[18]);  // mPDF 5.7.1
								} else {
									$this->floatmargins['R']['x'] = $fx;
									$this->floatmargins['R']['w'] = $w;
									$this->floatmargins['R']['y0'] = $fy;
									$this->floatmargins['R']['y1'] = $fy + $h;
									if ($skipln == 1) {
										$this->floatmargins['R']['skipline'] = true;
										$this->floatmargins['R']['id'] = count($this->floatbuffer) + 0;
										$objattr['skipline'] = true;
									}
									$this->floatbuffer[] = $objattr;
								}
							} else {
								$this->floatmargins['R']['x'] = $fx;
								$this->floatmargins['R']['w'] = $w;
								$this->floatmargins['R']['y0'] = $fy;
								$this->floatmargins['R']['y1'] = $fy + $h;
								if ($skipln == 1) {
									$this->floatmargins['R']['skipline'] = true;
									$this->floatmargins['R']['id'] = count($this->floatbuffer) + 0;
									$objattr['skipline'] = true;
								}
								$this->floatbuffer[] = $objattr;
							}
						} else if ($objattr['float'] == 'L') {
							// If L float already exists at this level
							$this->floatmargins['L']['skipline'] = false;
							if (isset($this->floatmargins['L']['y1']) && $this->floatmargins['L']['y1'] > 0 && $fy < $this->floatmargins['L']['y1']) {
								$this->floatmargins['L']['skipline'] = false;
								$this->WriteFlowingBlock($vetor[0], $vetor[18]);  // mPDF 5.7.1
							}
							// If R float already exists at this level
							else if (isset($this->floatmargins['R']['y1']) && $this->floatmargins['R']['y1'] > 0 && $fy < $this->floatmargins['R']['y1']) {
								// Final check distance between floats is not now too narrow to fit text
								$mw = 2 * $this->GetCharWidth('W', false);
								if (($this->blk[$this->blklvl]['inner_width'] - $w - $this->floatmargins['R']['w']) < $mw) {
									$this->WriteFlowingBlock($vetor[0], $vetor[18]);  // mPDF 5.7.1
								} else {
									$this->floatmargins['L']['x'] = $fx + $w;
									$this->floatmargins['L']['w'] = $w;
									$this->floatmargins['L']['y0'] = $fy;
									$this->floatmargins['L']['y1'] = $fy + $h;
									if ($skipln == 1) {
										$this->floatmargins['L']['skipline'] = true;
										$this->floatmargins['L']['id'] = count($this->floatbuffer) + 0;
										$objattr['skipline'] = true;
									}
									$this->floatbuffer[] = $objattr;
								}
							} else {
								$this->floatmargins['L']['x'] = $fx + $w;
								$this->floatmargins['L']['w'] = $w;
								$this->floatmargins['L']['y0'] = $fy;
								$this->floatmargins['L']['y1'] = $fy + $h;
								if ($skipln == 1) {
									$this->floatmargins['L']['skipline'] = true;
									$this->floatmargins['L']['id'] = count($this->floatbuffer) + 0;
									$objattr['skipline'] = true;
								}
								$this->floatbuffer[] = $objattr;
							}
						}
					} else {
						/* -- END CSS-IMAGE-FLOAT -- */
						$this->WriteFlowingBlock($vetor[0], (isset($vetor[18]) ? $vetor[18] : NULL));  // mPDF 5.7.1
						/* -- CSS-IMAGE-FLOAT -- */
					}
					/* -- END CSS-IMAGE-FLOAT -- */
				} // *TABLES*
			} // END If special content
			else { //THE text
				if ($this->tableLevel) {
					$paint_ht_corr = 0;
				} // To move the y up when new column/page started if div border needed
				else {
					$paint_ht_corr = $this->blk[$this->blklvl]['border_top']['w'];
				}

				if ($vetor[0] == "\n") { //We are reading a <BR> now turned into newline ("\n")
					if ($this->flowingBlockAttr['content']) {
						$this->finishFlowingBlock(false, 'br');
					} else if ($is_table) {
						$this->y+= $this->_computeLineheight($this->cellLineHeight);
					} else if (!$is_table) {
						$this->DivLn($this->lineheight);
						if ($this->ColActive) {
							$this->breakpoints[$this->CurrCol][] = $this->y;
						} // *COLUMNS*
					}
					// Added to correct for OddEven Margins
					if ($this->page != $oldpage) {
						if (($this->page - $oldpage) % 2 == 1) {
							$bak_x += $this->MarginCorrection;
						}
						$oldpage = $this->page;
						$y = $this->tMargin - $paint_ht_corr;
						$this->oldy = $this->tMargin - $paint_ht_corr;
						$old_height = 0;
					}
					$this->x = $bak_x;
					/* -- COLUMNS -- */
					// COLS
					// OR COLUMN CHANGE
					if ($this->CurrCol != $oldcolumn) {
						if ($this->directionality == 'rtl') { // *OTL*
							$bak_x -= ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap); // *OTL*
						} // *OTL*
						else { // *OTL*
							$bak_x += ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap);
						} // *OTL*
						$this->x = $bak_x;
						$oldcolumn = $this->CurrCol;
						$y = $this->y0 - $paint_ht_corr;
						$this->oldy = $this->y0 - $paint_ht_corr;
						$old_height = 0;
					}
					/* -- END COLUMNS -- */
					$this->newFlowingBlock($this->divwidth, $this->divheight, $align, $is_table, $blockstate, false, $blockdir, $table_draft);
				} else {
					$this->WriteFlowingBlock($vetor[0], $vetor[18]);  // mPDF 5.7.1
					// Added to correct for OddEven Margins
					if ($this->page != $oldpage) {
						if (($this->page - $oldpage) % 2 == 1) {
							$bak_x += $this->MarginCorrection;
							$this->x = $bak_x;
						}
						$oldpage = $this->page;
						$y = $this->tMargin - $paint_ht_corr;
						$this->oldy = $this->tMargin - $paint_ht_corr;
						$old_height = 0;
					}
					/* -- COLUMNS -- */
					// COLS
					// OR COLUMN CHANGE
					if ($this->CurrCol != $oldcolumn) {
						if ($this->directionality == 'rtl') { // *OTL*
							$bak_x -= ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap); // *OTL*
						} // *OTL*
						else { // *OTL*
							$bak_x += ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap);
						} // *OTL*
						$this->x = $bak_x;
						$oldcolumn = $this->CurrCol;
						$y = $this->y0 - $paint_ht_corr;
						$this->oldy = $this->y0 - $paint_ht_corr;
						$old_height = 0;
					}
					/* -- END COLUMNS -- */
				}
			}

			//Check if it is the last element. If so then finish printing the block
			if ($i == ($array_size - 1)) {
				$this->finishFlowingBlock(true); // true = END of flowing block
				// Added to correct for OddEven Margins
				if ($this->page != $oldpage) {
					if (($this->page - $oldpage) % 2 == 1) {
						$bak_x += $this->MarginCorrection;
						$this->x = $bak_x;
					}
					$oldpage = $this->page;
					$y = $this->tMargin - $paint_ht_corr;
					$this->oldy = $this->tMargin - $paint_ht_corr;
					$old_height = 0;
				}

				/* -- COLUMNS -- */
				// COLS
				// OR COLUMN CHANGE
				if ($this->CurrCol != $oldcolumn) {
					if ($this->directionality == 'rtl') { // *OTL*
						$bak_x -= ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap); // *OTL*
					} // *OTL*
					else { // *OTL*
						$bak_x += ($this->CurrCol - $oldcolumn) * ($this->ColWidth + $this->ColGap);
					} // *OTL*
					$this->x = $bak_x;
					$oldcolumn = $this->CurrCol;
					$y = $this->y0 - $paint_ht_corr;
					$this->oldy = $this->y0 - $paint_ht_corr;
					$old_height = 0;
				}
				/* -- END COLUMNS -- */
			}

			// RESETTING VALUES
			$this->SetTColor($this->ConvertColor(0));
			$this->SetDColor($this->ConvertColor(0));
			$this->SetFColor($this->ConvertColor(255));
			$this->colorarray = '';
			$this->spanbgcolorarray = '';
			$this->spanbgcolor = false;
			$this->spanborder = false;
			$this->spanborddet = array();
			$this->HREF = '';
			$this->textparam = array();
			$this->SetTextOutline();

			$this->textvar = 0x00; // mPDF 5.7.1
			$this->OTLtags = array();
			$this->textshadow = '';

			$this->currentfontfamily = '';
			$this->currentfontsize = '';
			$this->currentfontstyle = '';
			$this->currentLang = $this->default_lang;  // mPDF 6
			$this->RestrictUnicodeFonts($this->default_available_fonts); // mPDF 6
			/* -- TABLES -- */
			if ($this->tableLevel) {
				$this->SetLineHeight('', $this->table[1][1]['cellLineHeight']); // *TABLES*
			} else
			/* -- END TABLES -- */
			if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {
				$this->SetLineHeight('', $this->blk[$this->blklvl]['line_height']); // sets default line height
			}
			$this->ResetStyles();
			$this->lSpacingCSS = '';
			$this->wSpacingCSS = '';
			$this->fixedlSpacing = false;
			$this->minwSpacing = 0;
			$this->SetDash();
			$this->dash_on = false;
			$this->dotted_on = false;
		}//end of for(i=0;i<arraysize;i++)

		$this->Reset(); // mPDF 6
		// PAINT DIV BORDER	// DISABLED IN COLUMNS AS DOESN'T WORK WHEN BROKEN ACROSS COLS??
		if ((isset($this->blk[$this->blklvl]['border']) || isset($this->blk[$this->blklvl]['bgcolor']) || isset($this->blk[$this->blklvl]['box_shadow'])) && $blockstate && ($this->y != $this->oldy)) {
			$bottom_y = $this->y; // Does not include Bottom Margin
			if (isset($this->blk[$this->blklvl]['startpage']) && $this->blk[$this->blklvl]['startpage'] != $this->page && $blockstate != 1) {
				$this->PaintDivBB('pagetop', $blockstate);
			} else if ($blockstate != 1) {
				$this->PaintDivBB('', $blockstate);
			}
			$this->y = $bottom_y;
			$this->x = $bak_x;
		}

		// Reset Font
		$this->SetFontSize($this->default_font_size, false);
		if ($table_draft) {
			$ch = $this->y - $bak_y;
			$this->y = $bak_y;
			$this->x = $bak_x;
			return $ch;
		}
	}

	function _setDashBorder($style, $div, $cp, $side)
	{
		if ($style == 'dashed' && (($side == 'L' || $side == 'R') || ($side == 'T' && $div != 'pagetop' && !$cp) || ($side == 'B' && $div != 'pagebottom') )) {
			$dashsize = 2; // final dash will be this + 1*linewidth
			$dashsizek = 1.5; // ratio of Dash/Blank
			$this->SetDash($dashsize, ($dashsize / $dashsizek) + ($this->LineWidth * 2));
		} else if ($style == 'dotted' || ($side == 'T' && ($div == 'pagetop' || $cp)) || ($side == 'B' && $div == 'pagebottom')) {
			//Round join and cap
			$this->SetLineJoin(1);
			$this->SetLineCap(1);
			$this->SetDash(0.001, ($this->LineWidth * 3));
		}
	}

	function _setBorderLine($b, $k = 1)
	{
		$this->SetLineWidth($b['w'] / $k);
		$this->SetDColor($b['c']);
		if ($b['c'][0] == 5) { // RGBa
			$this->SetAlpha(ord($b['c'][4]) / 100, 'Normal', false, 'S') . "\n"; // mPDF 5.7.2
		} else if ($b['c'][0] == 6) { // CMYKa
			$this->SetAlpha(ord($b['c'][5]) / 100, 'Normal', false, 'S') . "\n"; // mPDF 5.7.2
		}
	}

	function PaintDivBB($divider = '', $blockstate = 0, $blvl = 0)
	{
		// Borders & backgrounds are done elsewhere for columns - messes up the repositioning in printcolumnbuffer
		if ($this->ColActive) {
			return;
		} // *COLUMNS*
		if ($this->keep_block_together) {
			return;
		} // mPDF 6
		$save_y = $this->y;
		if (!$blvl) {
			$blvl = $this->blklvl;
		}
		$x0 = $x1 = $y0 = $y1 = 0;

		// Added mPDF 3.0 Float DIV
		if (isset($this->blk[$blvl]['bb_painted'][$this->page]) && $this->blk[$blvl]['bb_painted'][$this->page]) {
			return;
		} // *CSS-FLOAT*

		if (isset($this->blk[$blvl]['x0'])) {
			$x0 = $this->blk[$blvl]['x0'];
		} // left
		if (isset($this->blk[$blvl]['y1'])) {
			$y1 = $this->blk[$blvl]['y1'];
		} // bottom
		// Added mPDF 3.0 Float DIV - ensures backgrounds/borders are drawn to bottom of page
		if ($y1 == 0) {
			if ($divider == 'pagebottom') {
				$y1 = $this->h - $this->bMargin;
			} else {
				$y1 = $this->y;
			}
		}

		if (isset($this->blk[$blvl]['startpage']) && $this->blk[$blvl]['startpage'] != $this->page) {
			$continuingpage = true;
		} else {
			$continuingpage = false;
		}

		if (isset($this->blk[$blvl]['y0'])) {
			$y0 = $this->blk[$blvl]['y0'];
		}
		$h = $y1 - $y0;
		$w = $this->blk[$blvl]['width'];
		$x1 = $x0 + $w;

		// Set border-widths as used here
		$border_top = $this->blk[$blvl]['border_top']['w'];
		$border_bottom = $this->blk[$blvl]['border_bottom']['w'];
		$border_left = $this->blk[$blvl]['border_left']['w'];
		$border_right = $this->blk[$blvl]['border_right']['w'];
		if (!$this->blk[$blvl]['border_top'] || $divider == 'pagetop' || $continuingpage) {
			$border_top = 0;
		}
		if (!$this->blk[$blvl]['border_bottom'] || $blockstate == 1 || $divider == 'pagebottom') {
			$border_bottom = 0;
		}

		$brTL_H = 0;
		$brTL_V = 0;
		$brTR_H = 0;
		$brTR_V = 0;
		$brBL_H = 0;
		$brBL_V = 0;
		$brBR_H = 0;
		$brBR_V = 0;

		$brset = false;
		/* -- BORDER-RADIUS -- */
		if (isset($this->blk[$blvl]['border_radius_TL_H'])) {
			$brTL_H = $this->blk[$blvl]['border_radius_TL_H'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_TL_V'])) {
			$brTL_V = $this->blk[$blvl]['border_radius_TL_V'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_TR_H'])) {
			$brTR_H = $this->blk[$blvl]['border_radius_TR_H'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_TR_V'])) {
			$brTR_V = $this->blk[$blvl]['border_radius_TR_V'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_BR_H'])) {
			$brBR_H = $this->blk[$blvl]['border_radius_BR_H'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_BR_V'])) {
			$brBR_V = $this->blk[$blvl]['border_radius_BR_V'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_BL_H'])) {
			$brBL_H = $this->blk[$blvl]['border_radius_BL_H'];
			$brset = true;
		}
		if (isset($this->blk[$blvl]['border_radius_BL_V'])) {
			$brBL_V = $this->blk[$blvl]['border_radius_BL_V'];
			$brset = true;
		}

		if (!$this->blk[$blvl]['border_top'] || $divider == 'pagetop' || $continuingpage) {
			$brTL_H = 0;
			$brTL_V = 0;
			$brTR_H = 0;
			$brTR_V = 0;
		}
		if (!$this->blk[$blvl]['border_bottom'] || $blockstate == 1 || $divider == 'pagebottom') {
			$brBL_H = 0;
			$brBL_V = 0;
			$brBR_H = 0;
			$brBR_V = 0;
		}

		// Disallow border-radius if it is smaller than the border width.
		if ($brTL_H < min($border_left, $border_top)) {
			$brTL_H = $brTL_V = 0;
		}
		if ($brTL_V < min($border_left, $border_top)) {
			$brTL_V = $brTL_H = 0;
		}
		if ($brTR_H < min($border_right, $border_top)) {
			$brTR_H = $brTR_V = 0;
		}
		if ($brTR_V < min($border_right, $border_top)) {
			$brTR_V = $brTR_H = 0;
		}
		if ($brBL_H < min($border_left, $border_bottom)) {
			$brBL_H = $brBL_V = 0;
		}
		if ($brBL_V < min($border_left, $border_bottom)) {
			$brBL_V = $brBL_H = 0;
		}
		if ($brBR_H < min($border_right, $border_bottom)) {
			$brBR_H = $brBR_V = 0;
		}
		if ($brBR_V < min($border_right, $border_bottom)) {
			$brBR_V = $brBR_H = 0;
		}

		// CHECK FOR radii that sum to > width or height of div ********
		$f = min($h / ($brTL_V + $brBL_V + 0.001), $h / ($brTR_V + $brBR_V + 0.001), $w / ($brTL_H + $brTR_H + 0.001), $w / ($brBL_H + $brBR_H + 0.001));
		if ($f < 1) {
			$brTL_H *= $f;
			$brTL_V *= $f;
			$brTR_H *= $f;
			$brTR_V *= $f;
			$brBL_H *= $f;
			$brBL_V *= $f;
			$brBR_H *= $f;
			$brBR_V *= $f;
		}
		/* -- END BORDER-RADIUS -- */

		$tbcol = $this->ConvertColor(255);
		for ($l = 0; $l <= $blvl; $l++) {
			if ($this->blk[$l]['bgcolor']) {
				$tbcol = $this->blk[$l]['bgcolorarray'];
			}
		}

		// BORDERS
		if (isset($this->blk[$blvl]['y0']) && $this->blk[$blvl]['y0']) {
			$y0 = $this->blk[$blvl]['y0'];
		}
		$h = $y1 - $y0;
		$w = $this->blk[$blvl]['width'];

		if ($this->blk[$blvl]['border_top'] && $divider != 'pagetop' && !$continuingpage) {
			$tbd = $this->blk[$blvl]['border_top'];

			$legend = '';
			$legbreakL = 0;
			$legbreakR = 0;
			// BORDER LEGEND
			if (isset($this->blk[$blvl]['border_legend']) && $this->blk[$blvl]['border_legend']) {
				$legend = $this->blk[$blvl]['border_legend']; // Same structure array as textbuffer
				$txt = $legend[0] = ltrim($legend[0]);
				if (!empty($legend[18])) {
					$this->otl->trimOTLdata($legend[18], true, false);
				} // *OTL*
				//Set font, size, style, color
				$this->SetFont($legend[4], $legend[2], $legend[11]);
				if (isset($legend[3]) && $legend[3]) {
					$cor = $legend[3];
					$this->SetTColor($cor);
				}
				$stringWidth = $this->GetStringWidth($txt, true, $legend[18], $legend[8]);
				$save_x = $this->x;
				$save_y = $this->y;
				$save_currentfontfamily = $this->FontFamily;
				$save_currentfontsize = $this->FontSizePt;
				$save_currentfontstyle = $this->FontStyle;
				$this->y = $y0 - $this->FontSize / 2 + $this->blk[$blvl]['border_top']['w'] / 2;
				$this->x = $x0 + $this->blk[$blvl]['padding_left'] + $this->blk[$blvl]['border_left']['w'];

				// Set the distance from the border line to the text ? make configurable variable
				$gap = 0.2 * $this->FontSize;
				$legbreakL = $this->x - $gap;
				$legbreakR = $this->x + $stringWidth + $gap;
				$this->magic_reverse_dir($txt, $this->blk[$blvl]['direction'], $legend[18]);
				$fill = '';
				$this->Cell($stringWidth, $this->FontSize, $txt, '', 0, 'C', $fill, '', 0, 0, 0, 'M', $fill, false, $legend[18], $legend[8]);
				// Reset
				$this->x = $save_x;
				$this->y = $save_y;
				$this->SetFont($save_currentfontfamily, $save_currentfontstyle, $save_currentfontsize);
				$this->SetTColor($this->ConvertColor(0));
			}

			if (isset($tbd['s']) && $tbd['s']) {
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('q');
					$this->SetLineWidth(0);
					$this->_out(sprintf('%.3F %.3F m ', ($x0) * _MPDFK, ($this->h - ($y0)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $border_left) * _MPDFK, ($this->h - ($y0 + $border_top)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w - $border_right) * _MPDFK, ($this->h - ($y0 + $border_top)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w) * _MPDFK, ($this->h - ($y0)) * _MPDFK));
					$this->_out(' h W n '); // Ends path no-op & Sets the clipping path
				}

				$this->_setBorderLine($tbd);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$legbreakL -= $border_top / 2; // because line cap different
					$legbreakR += $border_top / 2;
					$this->_setDashBorder($tbd['style'], $divider, $continuingpage, 'T');
				}
				/* -- BORDER-RADIUS -- */ else if (($brTL_V && $brTL_H) || ($brTR_V && $brTR_H) || $tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
				}
				$s = '';
				if ($brTR_H && $brTR_V) {
					$s .= ($this->_EllipseArc($x0 + $w - $brTR_H, $y0 + $brTR_V, $brTR_H - $border_top / 2, $brTR_V - $border_top / 2, 1, 2, true)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + $w) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + $w - ($border_top / 2)) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
				}
				/* -- BORDER-RADIUS -- */
				if ($brTL_H && $brTL_V) {
					if ($legend) {
						if ($legbreakR < ($x0 + $w - $brTR_H)) {
							$s .= (sprintf('%.3F %.3F l ', $legbreakR * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
						}
						if ($legbreakL > ($x0 + $brTL_H )) {
							$s .= (sprintf('%.3F %.3F m ', $legbreakL * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
							$s .= (sprintf('%.3F %.3F l ', ($x0 + $brTL_H ) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK) . "\n");
						} else {
							$s .= (sprintf('%.3F %.3F m ', ($x0 + $brTL_H ) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
						}
					} else {
						$s .= (sprintf('%.3F %.3F l ', ($x0 + $brTL_H ) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
					}
					$s .= ($this->_EllipseArc($x0 + $brTL_H, $y0 + $brTL_V, $brTL_H - $border_top / 2, $brTL_V - $border_top / 2, 2, 1)) . "\n";
				} else {
					/* -- END BORDER-RADIUS -- */
					if ($legend) {
						if ($legbreakR < ($x0 + $w)) {
							$s .= (sprintf('%.3F %.3F l ', $legbreakR * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
						}
						if ($legbreakL > ($x0)) {
							$s .= (sprintf('%.3F %.3F m ', $legbreakL * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
							if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
								$s .= (sprintf('%.3F %.3F l ', ($x0) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
							} else {
								$s .= (sprintf('%.3F %.3F l ', ($x0 + ($border_top / 2)) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
							}
						} else if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
							$s .= (sprintf('%.3F %.3F m ', ($x0) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
						} else {
							$s .= (sprintf('%.3F %.3F m ', ($x0 + $border_top / 2) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
						}
					} else if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
						$s .= (sprintf('%.3F %.3F l ', ($x0) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
					} else {
						$s .= (sprintf('%.3F %.3F l ', ($x0 + ($border_top / 2)) * _MPDFK, ($this->h - ($y0 + ($border_top / 2))) * _MPDFK)) . "\n";
					}
					/* -- BORDER-RADIUS -- */
				}
				/* -- END BORDER-RADIUS -- */
				$s .= 'S' . "\n";
				$this->_out($s);

				if ($tbd['style'] == 'double') {
					$this->SetLineWidth($tbd['w'] / 3);
					$this->SetDColor($tbcol);
					$this->_out($s);
				}
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('Q');
				}

				// Reset Corners and Dash off
				$this->SetLineWidth(0.1);
				$this->SetDColor($this->ConvertColor(0));
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		// Reinstate line above for dotted line divider when block border crosses a page
		//else if ($divider == 'pagetop' || $continuingpage) {

		if ($this->blk[$blvl]['border_bottom'] && $blockstate != 1 && $divider != 'pagebottom') {
			$tbd = $this->blk[$blvl]['border_bottom'];
			if (isset($tbd['s']) && $tbd['s']) {
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('q');
					$this->SetLineWidth(0);
					$this->_out(sprintf('%.3F %.3F m ', ($x0) * _MPDFK, ($this->h - ($y0 + $h)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $border_left) * _MPDFK, ($this->h - ($y0 + $h - $border_bottom)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w - $border_right) * _MPDFK, ($this->h - ($y0 + $h - $border_bottom)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w) * _MPDFK, ($this->h - ($y0 + $h)) * _MPDFK));
					$this->_out(' h W n '); // Ends path no-op & Sets the clipping path
				}

				$this->_setBorderLine($tbd);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], $divider, $continuingpage, 'B');
				}
				/* -- BORDER-RADIUS -- */ else if (($brBL_V && $brBL_H) || ($brBR_V && $brBR_H) || $tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
				}
				$s = '';
				if ($brBL_H && $brBL_V) {
					$s .= ($this->_EllipseArc($x0 + $brBL_H, $y0 + $h - $brBL_V, $brBL_H - $border_bottom / 2, $brBL_V - $border_bottom / 2, 3, 2, true)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F m ', ($x0) * _MPDFK, ($this->h - ($y0 + $h - ($border_bottom / 2))) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + ($border_bottom / 2)) * _MPDFK, ($this->h - ($y0 + $h - ($border_bottom / 2))) * _MPDFK)) . "\n";
				}
				/* -- BORDER-RADIUS -- */
				if ($brBR_H && $brBR_V) {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + $w - ($border_bottom / 2) - $brBR_H ) * _MPDFK, ($this->h - ($y0 + $h - ($border_bottom / 2))) * _MPDFK)) . "\n";
					$s .= ($this->_EllipseArc($x0 + $w - $brBR_H, $y0 + $h - $brBR_V, $brBR_H - $border_bottom / 2, $brBR_V - $border_bottom / 2, 4, 1)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + $w) * _MPDFK, ($this->h - ($y0 + $h - ($border_bottom / 2))) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + $w - ($border_bottom / 2)) * _MPDFK, ($this->h - ($y0 + $h - ($border_bottom / 2))) * _MPDFK)) . "\n";
				}
				$s .= 'S' . "\n";
				$this->_out($s);

				if ($tbd['style'] == 'double') {
					$this->SetLineWidth($tbd['w'] / 3);
					$this->SetDColor($tbcol);
					$this->_out($s);
				}
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('Q');
				}


				// Reset Corners and Dash off
				$this->SetLineWidth(0.1);
				$this->SetDColor($this->ConvertColor(0));
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		// Reinstate line below for dotted line divider when block border crosses a page
		//else if ($blockstate == 1 || $divider == 'pagebottom') {

		if ($this->blk[$blvl]['border_left']) {
			$tbd = $this->blk[$blvl]['border_left'];
			if (isset($tbd['s']) && $tbd['s']) {
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('q');
					$this->SetLineWidth(0);
					$this->_out(sprintf('%.3F %.3F m ', ($x0) * _MPDFK, ($this->h - ($y0)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $border_left) * _MPDFK, ($this->h - ($y0 + $border_top)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $border_left) * _MPDFK, ($this->h - ($y0 + $h - $border_bottom)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0) * _MPDFK, ($this->h - ($y0 + $h)) * _MPDFK));
					$this->_out(' h W n '); // Ends path no-op & Sets the clipping path
				}

				$this->_setBorderLine($tbd);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], $divider, $continuingpage, 'L');
				}
				/* -- BORDER-RADIUS -- */ else if (($brTL_V && $brTL_H) || ($brBL_V && $brBL_H) || $tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
				}
				$s = '';
				if ($brTL_V && $brTL_H) {
					$s .= ($this->_EllipseArc($x0 + $brTL_H, $y0 + $brTL_V, $brTL_H - $border_left / 2, $brTL_V - $border_left / 2, 2, 2, true)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + ($border_left / 2)) * _MPDFK, ($this->h - ($y0)) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + ($border_left / 2)) * _MPDFK, ($this->h - ($y0 + ($border_left / 2))) * _MPDFK)) . "\n";
				}
				/* -- BORDER-RADIUS -- */
				if ($brBL_V && $brBL_H) {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + ($border_left / 2)) * _MPDFK, ($this->h - ($y0 + $h - ($border_left / 2) - $brBL_V) ) * _MPDFK)) . "\n";
					$s .= ($this->_EllipseArc($x0 + $brBL_H, $y0 + $h - $brBL_V, $brBL_H - $border_left / 2, $brBL_V - $border_left / 2, 3, 1)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + ($border_left / 2)) * _MPDFK, ($this->h - ($y0 + $h) ) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + ($border_left / 2)) * _MPDFK, ($this->h - ($y0 + $h - ($border_left / 2)) ) * _MPDFK)) . "\n";
				}
				$s .= 'S' . "\n";
				$this->_out($s);

				if ($tbd['style'] == 'double') {
					$this->SetLineWidth($tbd['w'] / 3);
					$this->SetDColor($tbcol);
					$this->_out($s);
				}
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('Q');
				}

				// Reset Corners and Dash off
				$this->SetLineWidth(0.1);
				$this->SetDColor($this->ConvertColor(0));
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($this->blk[$blvl]['border_right']) {
			$tbd = $this->blk[$blvl]['border_right'];
			if (isset($tbd['s']) && $tbd['s']) {
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('q');
					$this->SetLineWidth(0);
					$this->_out(sprintf('%.3F %.3F m ', ($x0 + $w) * _MPDFK, ($this->h - ($y0)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w - $border_right) * _MPDFK, ($this->h - ($y0 + $border_top)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w - $border_right) * _MPDFK, ($this->h - ($y0 + $h - $border_bottom)) * _MPDFK));
					$this->_out(sprintf('%.3F %.3F l ', ($x0 + $w) * _MPDFK, ($this->h - ($y0 + $h)) * _MPDFK));
					$this->_out(' h W n '); // Ends path no-op & Sets the clipping path
				}

				$this->_setBorderLine($tbd);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], $divider, $continuingpage, 'R');
				}
				/* -- BORDER-RADIUS -- */ else if (($brTR_V && $brTR_H) || ($brBR_V && $brBR_H) || $tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
				}
				$s = '';
				if ($brBR_V && $brBR_H) {
					$s .= ($this->_EllipseArc($x0 + $w - $brBR_H, $y0 + $h - $brBR_V, $brBR_H - $border_right / 2, $brBR_V - $border_right / 2, 4, 2, true)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + $w - ($border_right / 2)) * _MPDFK, ($this->h - ($y0 + $h)) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F m ', ($x0 + $w - ($border_right / 2)) * _MPDFK, ($this->h - ($y0 + $h - ($border_right / 2))) * _MPDFK)) . "\n";
				}
				/* -- BORDER-RADIUS -- */
				if ($brTR_V && $brTR_H) {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + $w - ($border_right / 2)) * _MPDFK, ($this->h - ($y0 + ($border_right / 2) + $brTR_V) ) * _MPDFK)) . "\n";
					$s .= ($this->_EllipseArc($x0 + $w - $brTR_H, $y0 + $brTR_V, $brTR_H - $border_right / 2, $brTR_V - $border_right / 2, 1, 1)) . "\n";
				} else
				/* -- END BORDER-RADIUS -- */
				if ($tbd['style'] == 'solid' || $tbd['style'] == 'double') {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + $w - ($border_right / 2)) * _MPDFK, ($this->h - ($y0) ) * _MPDFK)) . "\n";
				} else {
					$s .= (sprintf('%.3F %.3F l ', ($x0 + $w - ($border_right / 2)) * _MPDFK, ($this->h - ($y0 + ($border_right / 2)) ) * _MPDFK)) . "\n";
				}
				$s .= 'S' . "\n";
				$this->_out($s);

				if ($tbd['style'] == 'double') {
					$this->SetLineWidth($tbd['w'] / 3);
					$this->SetDColor($tbcol);
					$this->_out($s);
				}
				if (!$brset && $tbd['style'] != 'dotted' && $tbd['style'] != 'dashed') {
					$this->_out('Q');
				}

				// Reset Corners and Dash off
				$this->SetLineWidth(0.1);
				$this->SetDColor($this->ConvertColor(0));
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}


		$this->SetDash();
		$this->y = $save_y;


		// BACKGROUNDS are disabled in columns/kbt/headers - messes up the repositioning in printcolumnbuffer
		if ($this->ColActive || $this->kwt || $this->keep_block_together) {
			return;
		}


		$bgx0 = $x0;
		$bgx1 = $x1;
		$bgy0 = $y0;
		$bgy1 = $y1;

		// Defined br values represent the radius of the outer curve - need to take border-width/2 from each radius for drawing the borders
		if (isset($this->blk[$blvl]['background_clip']) && $this->blk[$blvl]['background_clip'] == 'padding-box') {
			$brbgTL_H = max(0, $brTL_H - $this->blk[$blvl]['border_left']['w']);
			$brbgTL_V = max(0, $brTL_V - $this->blk[$blvl]['border_top']['w']);
			$brbgTR_H = max(0, $brTR_H - $this->blk[$blvl]['border_right']['w']);
			$brbgTR_V = max(0, $brTR_V - $this->blk[$blvl]['border_top']['w']);
			$brbgBL_H = max(0, $brBL_H - $this->blk[$blvl]['border_left']['w']);
			$brbgBL_V = max(0, $brBL_V - $this->blk[$blvl]['border_bottom']['w']);
			$brbgBR_H = max(0, $brBR_H - $this->blk[$blvl]['border_right']['w']);
			$brbgBR_V = max(0, $brBR_V - $this->blk[$blvl]['border_bottom']['w']);
			$bgx0 += $this->blk[$blvl]['border_left']['w'];
			$bgx1 -= $this->blk[$blvl]['border_right']['w'];
			if ($this->blk[$blvl]['border_top'] && $divider != 'pagetop' && !$continuingpage) {
				$bgy0 += $this->blk[$blvl]['border_top']['w'];
			}
			if ($this->blk[$blvl]['border_bottom'] && $blockstate != 1 && $divider != 'pagebottom') {
				$bgy1 -= $this->blk[$blvl]['border_bottom']['w'];
			}
		} else if (isset($this->blk[$blvl]['background_clip']) && $this->blk[$blvl]['background_clip'] == 'content-box') {
			$brbgTL_H = max(0, $brTL_H - $this->blk[$blvl]['border_left']['w'] - $this->blk[$blvl]['padding_left']);
			$brbgTL_V = max(0, $brTL_V - $this->blk[$blvl]['border_top']['w'] - $this->blk[$blvl]['padding_top']);
			$brbgTR_H = max(0, $brTR_H - $this->blk[$blvl]['border_right']['w'] - $this->blk[$blvl]['padding_right']);
			$brbgTR_V = max(0, $brTR_V - $this->blk[$blvl]['border_top']['w'] - $this->blk[$blvl]['padding_top']);
			$brbgBL_H = max(0, $brBL_H - $this->blk[$blvl]['border_left']['w'] - $this->blk[$blvl]['padding_left']);
			$brbgBL_V = max(0, $brBL_V - $this->blk[$blvl]['border_bottom']['w'] - $this->blk[$blvl]['padding_bottom']);
			$brbgBR_H = max(0, $brBR_H - $this->blk[$blvl]['border_right']['w'] - $this->blk[$blvl]['padding_right']);
			$brbgBR_V = max(0, $brBR_V - $this->blk[$blvl]['border_bottom']['w'] - $this->blk[$blvl]['padding_bottom']);
			$bgx0 += $this->blk[$blvl]['border_left']['w'] + $this->blk[$blvl]['padding_left'];
			$bgx1 -= $this->blk[$blvl]['border_right']['w'] + $this->blk[$blvl]['padding_right'];
			if (($this->blk[$blvl]['border_top']['w'] || $this->blk[$blvl]['padding_top']) && $divider != 'pagetop' && !$continuingpage) {
				$bgy0 += $this->blk[$blvl]['border_top']['w'] + $this->blk[$blvl]['padding_top'];
			}
			if (($this->blk[$blvl]['border_bottom']['w'] || $this->blk[$blvl]['padding_bottom']) && $blockstate != 1 && $divider != 'pagebottom') {
				$bgy1 -= $this->blk[$blvl]['border_bottom']['w'] + $this->blk[$blvl]['padding_bottom'];
			}
		} else {
			$brbgTL_H = $brTL_H;
			$brbgTL_V = $brTL_V;
			$brbgTR_H = $brTR_H;
			$brbgTR_V = $brTR_V;
			$brbgBL_H = $brBL_H;
			$brbgBL_V = $brBL_V;
			$brbgBR_H = $brBR_H;
			$brbgBR_V = $brBR_V;
		}

		// Set clipping path
		$s = ' q 0 w '; // Line width=0
		$s .= sprintf('%.3F %.3F m ', ($bgx0 + $brbgTL_H ) * _MPDFK, ($this->h - $bgy0) * _MPDFK); // start point TL before the arc
		/* -- BORDER-RADIUS -- */
		if ($brbgTL_H || $brbgTL_V) {
			$s .= $this->_EllipseArc($bgx0 + $brbgTL_H, $bgy0 + $brbgTL_V, $brbgTL_H, $brbgTL_V, 2); // segment 2 TL
		}
		/* -- END BORDER-RADIUS -- */
		$s .= sprintf('%.3F %.3F l ', ($bgx0) * _MPDFK, ($this->h - ($bgy1 - $brbgBL_V )) * _MPDFK); // line to BL
		/* -- BORDER-RADIUS -- */
		if ($brbgBL_H || $brbgBL_V) {
			$s .= $this->_EllipseArc($bgx0 + $brbgBL_H, $bgy1 - $brbgBL_V, $brbgBL_H, $brbgBL_V, 3); // segment 3 BL
		}
		/* -- END BORDER-RADIUS -- */
		$s .= sprintf('%.3F %.3F l ', ($bgx1 - $brbgBR_H ) * _MPDFK, ($this->h - ($bgy1)) * _MPDFK); // line to BR
		/* -- BORDER-RADIUS -- */
		if ($brbgBR_H || $brbgBR_V) {
			$s .= $this->_EllipseArc($bgx1 - $brbgBR_H, $bgy1 - $brbgBR_V, $brbgBR_H, $brbgBR_V, 4); // segment 4 BR
		}
		/* -- END BORDER-RADIUS -- */
		$s .= sprintf('%.3F %.3F l ', ($bgx1) * _MPDFK, ($this->h - ($bgy0 + $brbgTR_V)) * _MPDFK); // line to TR
		/* -- BORDER-RADIUS -- */
		if ($brbgTR_H || $brbgTR_V) {
			$s .= $this->_EllipseArc($bgx1 - $brbgTR_H, $bgy0 + $brbgTR_V, $brbgTR_H, $brbgTR_V, 1); // segment 1 TR
		}
		/* -- END BORDER-RADIUS -- */
		$s .= sprintf('%.3F %.3F l ', ($bgx0 + $brbgTL_H ) * _MPDFK, ($this->h - $bgy0) * _MPDFK); // line to TL
		// Box Shadow
		$shadow = '';
		if (isset($this->blk[$blvl]['box_shadow']) && $this->blk[$blvl]['box_shadow'] && $h > 0) {
			foreach ($this->blk[$blvl]['box_shadow'] AS $sh) {
				// Colors
				if ($sh['col']{0} == 1) {
					$colspace = 'Gray';
					if ($sh['col']{2} == 1) {
						$col1 = '1' . $sh['col'][1] . '1' . $sh['col'][3];
					} else {
						$col1 = '1' . $sh['col'][1] . '1' . chr(100);
					}
					$col2 = '1' . $sh['col'][1] . '1' . chr(0);
				} else if ($sh['col']{0} == 4) { // CMYK
					$colspace = 'CMYK';
					$col1 = '6' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . $sh['col'][4] . chr(100);
					$col2 = '6' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . $sh['col'][4] . chr(0);
				} else if ($sh['col']{0} == 5) { // RGBa
					$colspace = 'RGB';
					$col1 = '5' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . $sh['col'][4];
					$col2 = '5' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . chr(0);
				} else if ($sh['col']{0} == 6) { // CMYKa
					$colspace = 'CMYK';
					$col1 = '6' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . $sh['col'][4] . $sh['col'][5];
					$col2 = '6' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . $sh['col'][4] . chr(0);
				} else {
					$colspace = 'RGB';
					$col1 = '5' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . chr(100);
					$col2 = '5' . $sh['col'][1] . $sh['col'][2] . $sh['col'][3] . chr(0);
				}

				// Use clipping path as set above (and rectangle around page) to clip area outside box
				$shadow .= $s; // Use the clipping path with W*
				$shadow .= sprintf('0 %.3F m %.3F %.3F l ', $this->h * _MPDFK, $this->w * _MPDFK, $this->h * _MPDFK);
				$shadow .= sprintf('%.3F 0 l 0 0 l 0 %.3F l ', $this->w * _MPDFK, $this->h * _MPDFK);
				$shadow .= 'W n' . "\n";

				$sh['blur'] = abs($sh['blur']); // cannot have negative blur value
				// Ensure spread/blur do not make effective shadow width/height < 0
				// Could do more complex things but this just adjusts spread value
				if (-$sh['spread'] + $sh['blur'] / 2 > min($w / 2, $h / 2)) {
					$sh['spread'] = $sh['blur'] / 2 - min($w / 2, $h / 2) + 0.01;
				}
				// Shadow Offset
				if ($sh['x'] || $sh['y'])
					$shadow .= sprintf(' q 1 0 0 1 %.4F %.4F cm', $sh['x'] * _MPDFK, -$sh['y'] * _MPDFK) . "\n";

				// Set path for INNER shadow
				$shadow .= ' q 0 w ';
				$shadow .= $this->SetFColor($col1, true) . "\n";
				if ($col1{0} == 5 && ord($col1{4}) < 100) { // RGBa
					$shadow .= $this->SetAlpha(ord($col1{4}) / 100, 'Normal', true, 'F') . "\n";
				} else if ($col1{0} == 6 && ord($col1{5}) < 100) { // CMYKa
					$shadow .= $this->SetAlpha(ord($col1{5}) / 100, 'Normal', true, 'F') . "\n";
				} else if ($col1{0} == 1 && $col1{2} == 1 && ord($col1{3}) < 100) { // Gray
					$shadow .= $this->SetAlpha(ord($col1{3}) / 100, 'Normal', true, 'F') . "\n";
				}

				// Blur edges
				$mag = 0.551784; // Bezier Control magic number for 4-part spline for circle/ellipse
				$mag2 = 0.551784; // Bezier Control magic number to fill in edge of blurred rectangle
				$d1 = $sh['spread'] + $sh['blur'] / 2;
				$d2 = $sh['spread'] - $sh['blur'] / 2;
				$bl = $sh['blur'];
				$x00 = $x0 - $d1;
				$y00 = $y0 - $d1;
				$w00 = $w + $d1 * 2;
				$h00 = $h + $d1 * 2;

				// If any border-radius is greater width-negative spread(inner edge), ignore radii for shadow or screws up
				$flatten = false;
				if (max($brbgTR_H, $brbgTL_H, $brbgBR_H, $brbgBL_H) >= $w + $d2) {
					$flatten = true;
				}
				if (max($brbgTR_V, $brbgTL_V, $brbgBR_V, $brbgBL_V) >= $h + $d2) {
					$flatten = true;
				}


				// TOP RIGHT corner
				$p1x = $x00 + $w00 - $d1 - $brbgTR_H;
				$p1c2x = $p1x + ($d2 + $brbgTR_H) * $mag;
				$p1y = $y00 + $bl;
				$p2x = $x00 + $w00 - $d1 - $brbgTR_H;
				$p2c2x = $p2x + ($d1 + $brbgTR_H) * $mag;
				$p2y = $y00;
				$p2c1y = $p2y + $bl / 2;
				$p3x = $x00 + $w00;
				$p3c2x = $p3x - $bl / 2;
				$p3y = $y00 + $d1 + $brbgTR_V;
				$p3c1y = $p3y - ($d1 + $brbgTR_V) * $mag;
				$p4x = $x00 + $w00 - $bl;
				$p4y = $y00 + $d1 + $brbgTR_V;
				$p4c2y = $p4y - ($d2 + $brbgTR_V) * $mag;
				if (-$d2 > min($brbgTR_H, $brbgTR_V) || $flatten) {
					$p1x = $x00 + $w00 - $bl;
					$p1c2x = $p1x;
					$p2x = $x00 + $w00 - $bl;
					$p2c2x = $p2x + $bl * $mag2;
					$p3y = $y00 + $bl;
					$p3c1y = $p3y - $bl * $mag2;
					$p4y = $y00 + $bl;
					$p4c2y = $p4y;
				}

				$shadow .= sprintf('%.3F %.3F m ', ($p1x ) * _MPDFK, ($this->h - ($p1y )) * _MPDFK);
				$shadow .= sprintf('%.3F %.3F %.3F %.3F %.3F %.3F c ', ($p1c2x) * _MPDFK, ($this->h - ($p1y)) * _MPDFK, ($p4x) * _MPDFK, ($this->h - ($p4c2y)) * _MPDFK, ($p4x) * _MPDFK, ($this->h - ($p4y)) * _MPDFK);
				$patch_array[0]['f'] = 0;
				$patch_array[0]['points'] = array($p1x, $p1y, $p1x, $p1y,
					$p2x, $p2c1y, $p2x, $p2y, $p2c2x, $p2y,
					$p3x, $p3c1y, $p3x, $p3y, $p3c2x, $p3y,
					$p4x, $p4y, $p4x, $p4y, $p4x, $p4c2y,
					$p1c2x, $p1y);
				$patch_array[0]['colors'] = array($col1, $col2, $col2, $col1);


				// RIGHT
				$p1x = $x00 + $w00; // control point only matches p3 preceding
				$p1y = $y00 + $d1 + $brbgTR_V;
				$p2x = $x00 + $w00 - $bl; // control point only matches p4 preceding
				$p2y = $y00 + $d1 + $brbgTR_V;
				$p3x = $x00 + $w00 - $bl;
				$p3y = $y00 + $h00 - $d1 - $brbgBR_V;
				$p4x = $x00 + $w00;
				$p4c1x = $p4x - $bl / 2;
				$p4y = $y00 + $h00 - $d1 - $brbgBR_V;
				if (-$d2 > min($brbgTR_H, $brbgTR_V) || $flatten) {
					$p1y = $y00 + $bl;
					$p2y = $y00 + $bl;
				}
				if (-$d2 > min($brbgBR_H, $brbgBR_V) || $flatten) {
					$p3y = $y00 + $h00 - $bl;
					$p4y = $y00 + $h00 - $bl;
				}

				$shadow .= sprintf('%.3F %.3F l ', ($p3x ) * _MPDFK, ($this->h - ($p3y )) * _MPDFK);
				$patch_array[1]['f'] = 2;
				$patch_array[1]['points'] = array($p2x, $p2y,
					$p3x, $p3y, $p3x, $p3y, $p3x, $p3y,
					$p4c1x, $p4y, $p4x, $p4y, $p4x, $p4y,
					$p1x, $p1y);
				$patch_array[1]['colors'] = array($col1, $col2);


				// BOTTOM RIGHT corner
				$p1x = $x00 + $w00 - $bl;  // control points only matches p3 preceding
				$p1y = $y00 + $h00 - $d1 - $brbgBR_V;
				$p1c2y = $p1y + ($d2 + $brbgBR_V) * $mag;
				$p2x = $x00 + $w00;     // control point only matches p4 preceding
				$p2y = $y00 + $h00 - $d1 - $brbgBR_V;
				$p2c2y = $p2y + ($d1 + $brbgBR_V) * $mag;
				$p3x = $x00 + $w00 - $d1 - $brbgBR_H;
				$p3c1x = $p3x + ($d1 + $brbgBR_H) * $mag;
				$p3y = $y00 + $h00;
				$p3c2y = $p3y - $bl / 2;
				$p4x = $x00 + $w00 - $d1 - $brbgBR_H;
				$p4c2x = $p4x + ($d2 + $brbgBR_H) * $mag;
				$p4y = $y00 + $h00 - $bl;

				if (-$d2 > min($brbgBR_H, $brbgBR_V) || $flatten) {
					$p1y = $y00 + $h00 - $bl;
					$p1c2y = $p1y;
					$p2y = $y00 + $h00 - $bl;
					$p2c2y = $p2y + $bl * $mag2;
					$p3x = $x00 + $w00 - $bl;
					$p3c1x = $p3x + $bl * $mag2;
					$p4x = $x00 + $w00 - $bl;
					$p4c2x = $p4x;
				}

				$shadow .= sprintf('%.3F %.3F %.3F %.3F %.3F %.3F c ', ($p1x) * _MPDFK, ($this->h - ($p1c2y)) * _MPDFK, ($p4c2x) * _MPDFK, ($this->h - ($p4y)) * _MPDFK, ($p4x) * _MPDFK, ($this->h - ($p4y)) * _MPDFK);
				$patch_array[2]['f'] = 2;
				$patch_array[2]['points'] = array($p2x, $p2c2y,
					$p3c1x, $p3y, $p3x, $p3y, $p3x, $p3c2y,
					$p4x, $p4y, $p4x, $p4y, $p4c2x, $p4y,
					$p1x, $p1c2y);
				$patch_array[2]['colors'] = array($col2, $col1);



				// BOTTOM
				$p1x = $x00 + $w00 - $d1 - $brbgBR_H; // control point only matches p3 preceding
				$p1y = $y00 + $h00;
				$p2x = $x00 + $w00 - $d1 - $brbgBR_H; // control point only matches p4 preceding
				$p2y = $y00 + $h00 - $bl;
				$p3x = $x00 + $d1 + $brbgBL_H;
				$p3y = $y00 + $h00 - $bl;
				$p4x = $x00 + $d1 + $brbgBL_H;
				$p4y = $y00 + $h00;
				$p4c1y = $p4y - $bl / 2;

				if (-$d2 > min($brbgBR_H, $brbgBR_V) || $flatten) {
					$p1x = $x00 + $w00 - $bl;
					$p2x = $x00 + $w00 - $bl;
				}
				if (-$d2 > min($brbgBL_H, $brbgBL_V) || $flatten) {
					$p3x = $x00 + $bl;
					$p4x = $x00 + $bl;
				}

				$shadow .= sprintf('%.3F %.3F l ', ($p3x ) * _MPDFK, ($this->h - ($p3y )) * _MPDFK);
				$patch_array[3]['f'] = 2;
				$patch_array[3]['points'] = array($p2x, $p2y,
					$p3x, $p3y, $p3x, $p3y, $p3x, $p3y,
					$p4x, $p4c1y, $p4x, $p4y, $p4x, $p4y,
					$p1x, $p1y);
				$patch_array[3]['colors'] = array($col1, $col2);

				// BOTTOM LEFT corner
				$p1x = $x00 + $d1 + $brbgBL_H;
				$p1c2x = $p1x - ($d2 + $brbgBL_H) * $mag; // control points only matches p3 preceding
				$p1y = $y00 + $h00 - $bl;
				$p2x = $x00 + $d1 + $brbgBL_H;
				$p2c2x = $p2x - ($d1 + $brbgBL_H) * $mag; // control point only matches p4 preceding
				$p2y = $y00 + $h00;
				$p3x = $x00;
				$p3c2x = $p3x + $bl / 2;
				$p3y = $y00 + $h00 - $d1 - $brbgBL_V;
				$p3c1y = $p3y + ($d1 + $brbgBL_V) * $mag;
				$p4x = $x00 + $bl;
				$p4y = $y00 + $h00 - $d1 - $brbgBL_V;
				$p4c2y = $p4y + ($d2 + $brbgBL_V) * $mag;
				if (-$d2 > min($brbgBL_H, $brbgBL_V) || $flatten) {
					$p1x = $x00 + $bl;
					$p1c2x = $p1x;
					$p2x = $x00 + $bl;
					$p2c2x = $p2x - $bl * $mag2;
					$p3y = $y00 + $h00 - $bl;
					$p3c1y = $p3y + $bl * $mag2;
					$p4y = $y00 + $h00 - $bl;
					$p4c2y = $p4y;
				}

				$shadow .= sprintf('%.3F %.3F %.3F %.3F %.3F %.3F c ', ($p1c2x) * _MPDFK, ($this->h - ($p1y)) * _MPDFK, ($p4x) * _MPDFK, ($this->h - ($p4c2y)) * _MPDFK, ($p4x) * _MPDFK, ($this->h - ($p4y)) * _MPDFK);
				$patch_array[4]['f'] = 2;
				$patch_array[4]['points'] = array($p2c2x, $p2y,
					$p3x, $p3c1y, $p3x, $p3y, $p3c2x, $p3y,
					$p4x, $p4y, $p4x, $p4y, $p4x, $p4c2y,
					$p1c2x, $p1y);
				$patch_array[4]['colors'] = array($col2, $col1);


				// LEFT - joins on the right (C3-C4 of previous): f = 2
				$p1x = $x00; // control point only matches p3 preceding
				$p1y = $y00 + $h00 - $d1 - $brbgBL_V;
				$p2x = $x00 + $bl; // control point only matches p4 preceding
				$p2y = $y00 + $h00 - $d1 - $brbgBL_V;
				$p3x = $x00 + $bl;
				$p3y = $y00 + $d1 + $brbgTL_V;
				$p4x = $x00;
				$p4c1x = $p4x + $bl / 2;
				$p4y = $y00 + $d1 + $brbgTL_V;
				if (-$d2 > min($brbgBL_H, $brbgBL_V) || $flatten) {
					$p1y = $y00 + $h00 - $bl;
					$p2y = $y00 + $h00 - $bl;
				}
				if (-$d2 > min($brbgTL_H, $brbgTL_V) || $flatten) {
					$p3y = $y00 + $bl;
					$p4y = $y00 + $bl;
				}

				$shadow .= sprintf('%.3F %.3F l ', ($p3x ) * _MPDFK, ($this->h - ($p3y )) * _MPDFK);
				$patch_array[5]['f'] = 2;
				$patch_array[5]['points'] = array($p2x, $p2y,
					$p3x, $p3y, $p3x, $p3y, $p3x, $p3y,
					$p4c1x, $p4y, $p4x, $p4y, $p4x, $p4y,
					$p1x, $p1y);
				$patch_array[5]['colors'] = array($col1, $col2);

				// TOP LEFT corner
				$p1x = $x00 + $bl;  // control points only matches p3 preceding
				$p1y = $y00 + $d1 + $brbgTL_V;
				$p1c2y = $p1y - ($d2 + $brbgTL_V) * $mag;
				$p2x = $x00;   // control point only matches p4 preceding
				$p2y = $y00 + $d1 + $brbgTL_V;
				$p2c2y = $p2y - ($d1 + $brbgTL_V) * $mag;
				$p3x = $x00 + $d1 + $brbgTL_H;
				$p3c1x = $p3x - ($d1 + $brbgTL_H) * $mag;
				$p3y = $y00;
				$p3c2y = $p3y + $bl / 2;
				$p4x = $x00 + $d1 + $brbgTL_H;
				$p4c2x = $p4x - ($d2 + $brbgTL_H) * $mag;
				$p4y = $y00 + $bl;

				if (-$d2 > min($brbgTL_H, $brbgTL_V) || $flatten) {
					$p1y = $y00 + $bl;
					$p1c2y = $p1y;
					$p2y = $y00 + $bl;
					$p2c2y = $p2y - $bl * $mag2;
					$p3x = $x00 + $bl;
					$p3c1x = $p3x - $bl * $mag2;
					$p4x = $x00 + $bl;
					$p4c2x = $p4x;
				}

				$shadow .= sprintf('%.3F %.3F %.3F %.3F %.3F %.3F c ', ($p1x) * _MPDFK, ($this->h - ($p1c2y)) * _MPDFK, ($p4c2x) * _MPDFK, ($this->h - ($p4y)) * _MPDFK, ($p4x) * _MPDFK, ($this->h - ($p4y)) * _MPDFK);
				$patch_array[6]['f'] = 2;
				$patch_array[6]['points'] = array($p2x, $p2c2y,
					$p3c1x, $p3y, $p3x, $p3y, $p3x, $p3c2y,
					$p4x, $p4y, $p4x, $p4y, $p4c2x, $p4y,
					$p1x, $p1c2y);
				$patch_array[6]['colors'] = array($col2, $col1);


				// TOP - joins on the right (C3-C4 of previous): f = 2
				$p1x = $x00 + $d1 + $brbgTL_H; // control point only matches p3 preceding
				$p1y = $y00;
				$p2x = $x00 + $d1 + $brbgTL_H; // control point only matches p4 preceding
				$p2y = $y00 + $bl;
				$p3x = $x00 + $w00 - $d1 - $brbgTR_H;
				$p3y = $y00 + $bl;
				$p4x = $x00 + $w00 - $d1 - $brbgTR_H;
				$p4y = $y00;
				$p4c1y = $p4y + $bl / 2;
				if (-$d2 > min($brbgTL_H, $brbgTL_V) || $flatten) {
					$p1x = $x00 + $bl;
					$p2x = $x00 + $bl;
				}
				if (-$d2 > min($brbgTR_H, $brbgTR_V) || $flatten) {
					$p3x = $x00 + $w00 - $bl;
					$p4x = $x00 + $w00 - $bl;
				}

				$shadow .= sprintf('%.3F %.3F l ', ($p3x ) * _MPDFK, ($this->h - ($p3y )) * _MPDFK);
				$patch_array[7]['f'] = 2;
				$patch_array[7]['points'] = array($p2x, $p2y,
					$p3x, $p3y, $p3x, $p3y, $p3x, $p3y,
					$p4x, $p4c1y, $p4x, $p4y, $p4x, $p4y,
					$p1x, $p1y);
				$patch_array[7]['colors'] = array($col1, $col2);

				$shadow .= ' h f Q ' . "\n"; // Close path and Fill the inner solid shadow

				if ($bl)
					$shadow .= $this->grad->CoonsPatchMesh($x00, $y00, $w00, $h00, $patch_array, $x00, $x00 + $w00, $y00, $y00 + $h00, $colspace, true);

				if ($sh['x'] || $sh['y'])
					$shadow .= ' Q' . "\n";  // Shadow Offset
				$shadow .= ' Q' . "\n"; // Ends path no-op & Sets the clipping path
			}
		}

		$s .= ' W n '; // Ends path no-op & Sets the clipping path

		if ($this->blk[$blvl]['bgcolor']) {
			$this->pageBackgrounds[$blvl][] = array('x' => $x0, 'y' => $y0, 'w' => $w, 'h' => $h, 'col' => $this->blk[$blvl]['bgcolorarray'], 'clippath' => $s, 'visibility' => $this->visibility, 'shadow' => $shadow, 'z-index' => $this->current_layer);
		} else if ($shadow) {
			$this->pageBackgrounds[$blvl][] = array('shadowonly' => true, 'col' => '', 'clippath' => '', 'visibility' => $this->visibility, 'shadow' => $shadow, 'z-index' => $this->current_layer);
		}

	

		// Float DIV
		$this->blk[$blvl]['bb_painted'][$this->page] = true;
	}


	function PaintDivLnBorder($state = 0, $blvl = 0, $h)
	{
		// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
		$this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y + $h;

		$save_y = $this->y;

		$w = $this->blk[$blvl]['width'];
		$x0 = $this->x;    // left
		$y0 = $this->y;    // top
		$x1 = $this->x + $w;   // bottom
		$y1 = $this->y + $h;   // bottom

		if ($this->blk[$blvl]['border_top'] && ($state == 1 || $state == 3)) {
			$tbd = $this->blk[$blvl]['border_top'];
			if (isset($tbd['s']) && $tbd['s']) {
				$this->_setBorderLine($tbd);
				$this->y = $y0 + ($tbd['w'] / 2);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], '', $continuingpage, 'T');
					$this->Line($x0 + ($tbd['w'] / 2), $this->y, $x0 + $w - ($tbd['w'] / 2), $this->y);
				} else {
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
					$this->Line($x0, $this->y, $x0 + $w, $this->y);
				}
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($this->blk[$blvl]['border_left']) {
			$tbd = $this->blk[$blvl]['border_left'];
			if (isset($tbd['s']) && $tbd['s']) {
				$this->_setBorderLine($tbd);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->y = $y0 + ($tbd['w'] / 2);
					$this->_setDashBorder($tbd['style'], '', $continuingpage, 'L');
					$this->Line($x0 + ($tbd['w'] / 2), $this->y, $x0 + ($tbd['w'] / 2), $y0 + $h - ($tbd['w'] / 2));
				} else {
					$this->y = $y0;
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
					$this->Line($x0 + ($tbd['w'] / 2), $this->y, $x0 + ($tbd['w'] / 2), $y0 + $h);
				}
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($this->blk[$blvl]['border_right']) {
			$tbd = $this->blk[$blvl]['border_right'];
			if (isset($tbd['s']) && $tbd['s']) {
				$this->_setBorderLine($tbd);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->y = $y0 + ($tbd['w'] / 2);
					$this->_setDashBorder($tbd['style'], '', $continuingpage, 'R');
					$this->Line($x0 + $w - ($tbd['w'] / 2), $this->y, $x0 + $w - ($tbd['w'] / 2), $y0 + $h - ($tbd['w'] / 2));
				} else {
					$this->y = $y0;
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
					$this->Line($x0 + $w - ($tbd['w'] / 2), $this->y, $x0 + $w - ($tbd['w'] / 2), $y0 + $h);
				}
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($this->blk[$blvl]['border_bottom'] && $state > 1) {
			$tbd = $this->blk[$blvl]['border_bottom'];
			if (isset($tbd['s']) && $tbd['s']) {
				$this->_setBorderLine($tbd);
				$this->y = $y0 + $h - ($tbd['w'] / 2);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], '', $continuingpage, 'B');
					$this->Line($x0 + ($tbd['w'] / 2), $this->y, $x0 + $w - ($tbd['w'] / 2), $this->y);
				} else {
					$this->SetLineJoin(0);
					$this->SetLineCap(0);
					$this->Line($x0, $this->y, $x0 + $w, $this->y);
				}
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		$this->SetDash();
		$this->y = $save_y;
	}

	function PaintImgBorder($objattr, $is_table)
	{
		// Borders are disabled in columns - messes up the repositioning in printcolumnbuffer
		if ($this->ColActive) {
			return;
		} // *COLUMNS*
		if ($is_table) {
			$k = $this->shrin_k;
		} else {
			$k = 1;
		}
		$h = (isset($objattr['BORDER-HEIGHT']) ? $objattr['BORDER-HEIGHT'] : 0);
		$w = (isset($objattr['BORDER-WIDTH']) ? $objattr['BORDER-WIDTH'] : 0);
		$x0 = (isset($objattr['BORDER-X']) ? $objattr['BORDER-X'] : 0);
		$y0 = (isset($objattr['BORDER-Y']) ? $objattr['BORDER-Y'] : 0);

		// BORDERS
		if ($objattr['border_top']) {
			$tbd = $objattr['border_top'];
			if (!empty($tbd['s'])) {
				$this->_setBorderLine($tbd, $k);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], '', '', 'T');
				}
				$this->Line($x0, $y0, $x0 + $w, $y0);
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($objattr['border_left']) {
			$tbd = $objattr['border_left'];
			if (!empty($tbd['s'])) {
				$this->_setBorderLine($tbd, $k);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], '', '', 'L');
				}
				$this->Line($x0, $y0, $x0, $y0 + $h);
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($objattr['border_right']) {
			$tbd = $objattr['border_right'];
			if (!empty($tbd['s'])) {
				$this->_setBorderLine($tbd, $k);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], '', '', 'R');
				}
				$this->Line($x0 + $w, $y0, $x0 + $w, $y0 + $h);
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		if ($objattr['border_bottom']) {
			$tbd = $objattr['border_bottom'];
			if (!empty($tbd['s'])) {
				$this->_setBorderLine($tbd, $k);
				if ($tbd['style'] == 'dotted' || $tbd['style'] == 'dashed') {
					$this->_setDashBorder($tbd['style'], '', '', 'B');
				}
				$this->Line($x0, $y0 + $h, $x0 + $w, $y0 + $h);
				// Reset Corners and Dash off
				$this->SetLineJoin(2);
				$this->SetLineCap(2);
				$this->SetDash();
			}
		}
		$this->SetDash();
		$this->SetAlpha(1);
	}

	/* -- END HTML-CSS -- */

	function Reset()
	{
		$this->SetTColor($this->ConvertColor(0));
		$this->SetDColor($this->ConvertColor(0));
		$this->SetFColor($this->ConvertColor(255));
		$this->SetAlpha(1);
		$this->colorarray = '';

		$this->spanbgcolorarray = '';
		$this->spanbgcolor = false;
		$this->spanborder = false;
		$this->spanborddet = array();

		$this->ResetStyles();

		$this->HREF = '';
		$this->textparam = array();
		$this->SetTextOutline();

		$this->textvar = 0x00; // mPDF 5.7.1
		$this->OTLtags = array();
		$this->textshadow = '';

		$this->currentLang = $this->default_lang;  // mPDF 6
		$this->RestrictUnicodeFonts($this->default_available_fonts); // mPDF 6
		$this->SetFont($this->default_font, '', 0, false);
		$this->SetFontSize($this->default_font_size, false);

		$this->currentfontfamily = '';
		$this->currentfontsize = '';
		$this->currentfontstyle = '';

		/* -- TABLES -- */
		if ($this->tableLevel && isset($this->table[1][1]['cellLineHeight'])) {
			$this->SetLineHeight('', $this->table[1][1]['cellLineHeight']); // *TABLES*
		} else
		/* -- END TABLES -- */
		if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {
			$this->SetLineHeight('', $this->blk[$this->blklvl]['line_height']); // sets default line height
		}

		$this->lSpacingCSS = '';
		$this->wSpacingCSS = '';
		$this->fixedlSpacing = false;
		$this->minwSpacing = 0;
		$this->SetDash(); //restore to no dash
		$this->dash_on = false;
		$this->dotted_on = false;
		$this->divwidth = 0;
		$this->divheight = 0;
		$this->cellTextAlign = '';
		$this->cellLineHeight = '';
		$this->cellLineStackingStrategy = '';
		$this->cellLineStackingShift = '';
		$this->oldy = -1;

		$bodystyle = array();
		if (isset($this->cssmgr->CSS['BODY']['FONT-STYLE'])) {
			$bodystyle['FONT-STYLE'] = $this->cssmgr->CSS['BODY']['FONT-STYLE'];
		}
		if (isset($this->cssmgr->CSS['BODY']['FONT-WEIGHT'])) {
			$bodystyle['FONT-WEIGHT'] = $this->cssmgr->CSS['BODY']['FONT-WEIGHT'];
		}
		if (isset($this->cssmgr->CSS['BODY']['COLOR'])) {
			$bodystyle['COLOR'] = $this->cssmgr->CSS['BODY']['COLOR'];
		}
		if (isset($bodystyle)) {
			$this->setCSS($bodystyle, 'BLOCK', 'BODY');
		}
	}

	/* -- HTML-CSS -- */

	function ReadMetaTags($html)
	{
		// changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
		$regexp = '/ (\\w+?)=([^\\s>"]+)/si';
		$html = preg_replace($regexp, " \$1=\"\$2\"", $html);
		if (preg_match('/<title>(.*?)<\/title>/si', $html, $m)) {
			$this->SetTitle($m[1]);
		}
		preg_match_all('/<meta [^>]*?(name|content)="([^>]*?)" [^>]*?(name|content)="([^>]*?)".*?>/si', $html, $aux);
		$firstattr = $aux[1];
		$secondattr = $aux[3];
		for ($i = 0; $i < count($aux[0]); $i++) {

			$name = ( strtoupper($firstattr[$i]) == "NAME" ) ? strtoupper($aux[2][$i]) : strtoupper($aux[4][$i]);
			$content = ( strtoupper($firstattr[$i]) == "CONTENT" ) ? $aux[2][$i] : $aux[4][$i];
			switch ($name) {
				case "KEYWORDS": $this->SetKeywords($content);
					break;
				case "AUTHOR": $this->SetAuthor($content);
					break;
				case "DESCRIPTION": $this->SetSubject($content);
					break;
			}
		}
	}

	function ReadCharset($html)
	{
		// Charset conversion
		if ($this->allow_charset_conversion) {
			if (preg_match('/<head.*charset=([^\'\"\s]*).*<\/head>/si', $html, $m)) {
				if (strtoupper($m[1]) != 'UTF-8') {
					$this->charset_in = strtoupper($m[1]);
				}
			}
		}
	}

	function setCSS($arrayaux, $type = '', $tag = '')
	{ // type= INLINE | BLOCK | TABLECELL // tag= BODY
		if (!is_array($arrayaux))
			return; //Removes PHP Warning


// mPDF 5.7.3  inline text-decoration parameters
		$preceeding_fontkey = $this->FontFamily . $this->FontStyle;
		$preceeding_fontsize = $this->FontSize;
		$spanbordset = false;
		$spanbgset = false;
		// mPDF 6
		$prevlevel = (($this->blklvl == 0) ? 0 : $this->blklvl - 1);

		// Set font size first so that e.g. MARGIN 0.83em works on font size for this element
		if (isset($arrayaux['FONT-SIZE'])) {
			$v = $arrayaux['FONT-SIZE'];
			if (is_numeric($v[0])) {
				if ($type == 'BLOCK' && $this->blklvl > 0 && isset($this->blk[$this->blklvl - 1]['InlineProperties']) && isset($this->blk[$this->blklvl - 1]['InlineProperties']['size'])) {
					$mmsize = $this->ConvertSize($v, $this->blk[$this->blklvl - 1]['InlineProperties']['size']);
				} else if ($type == 'TABLECELL') {
					$mmsize = $this->ConvertSize($v, $this->default_font_size / _MPDFK);
				} else {
					$mmsize = $this->ConvertSize($v, $this->FontSize);
				}
				$this->SetFontSize($mmsize * (_MPDFK), false); //Get size in points (pt)
			} else {
				$v = strtoupper($v);
				if (isset($this->fontsizes[$v])) {
					$this->SetFontSize($this->fontsizes[$v] * $this->default_font_size, false);
				}
			}
			if ($tag == 'BODY') {
				$this->SetDefaultFontSize($this->FontSizePt);
			}
		}

		// mPDF 6
		if (isset($arrayaux['LANG']) && $arrayaux['LANG']) {
			if ($this->autoLangToFont && !$this->usingCoreFont) {
				if ($arrayaux['LANG'] != $this->default_lang && $arrayaux['LANG'] != 'UTF-8') {
					list ($coreSuitable, $mpdf_pdf_unifont) = GetLangOpts($arrayaux['LANG'], $this->useAdobeCJK, $this->fontdata);
					if ($mpdf_pdf_unifont) {
						$arrayaux['FONT-FAMILY'] = $mpdf_pdf_unifont;
					}
					if ($tag == 'BODY') {
						$this->default_lang = $arrayaux['LANG'];
					}
				}
			}
			$this->currentLang = $arrayaux['LANG'];
		}

		// FOR INLINE and BLOCK OR 'BODY'
		if (isset($arrayaux['FONT-FAMILY'])) {
			$v = $arrayaux['FONT-FAMILY'];
			//If it is a font list, get all font types
			$aux_fontlist = explode(",", $v);
			$found = 0;
			foreach ($aux_fontlist AS $f) {
				$fonttype = trim($f);
				$fonttype = preg_replace('/["\']*(.*?)["\']*/', '\\1', $fonttype);
				$fonttype = preg_replace('/ /', '', $fonttype);
				$v = strtolower(trim($fonttype));
				if (isset($this->fonttrans[$v]) && $this->fonttrans[$v]) {
					$v = $this->fonttrans[$v];
				}
				if ((!$this->onlyCoreFonts && in_array($v, $this->available_unifonts)) ||
					in_array($v, array('ccourier', 'ctimes', 'chelvetica')) ||
					($this->onlyCoreFonts && in_array($v, array('courier', 'times', 'helvetica', 'arial'))) ||
					in_array($v, array('sjis', 'uhc', 'big5', 'gb'))) {
					$fonttype = $v;
					$found = 1;
					break;
				}
			}
			if (!$found) {
				foreach ($aux_fontlist AS $f) {
					$fonttype = trim($f);
					$fonttype = preg_replace('/["\']*(.*?)["\']*/', '\\1', $fonttype);
					$fonttype = preg_replace('/ /', '', $fonttype);
					$v = strtolower(trim($fonttype));
					if (isset($this->fonttrans[$v]) && $this->fonttrans[$v]) {
						$v = $this->fonttrans[$v];
					}
					if (in_array($v, $this->sans_fonts) || in_array($v, $this->serif_fonts) || in_array($v, $this->mono_fonts)) {
						$fonttype = $v;
						break;
					}
				}
			}

			if ($tag == 'BODY') {
				$this->SetDefaultFont($fonttype);
			}
			$this->SetFont($fonttype, $this->currentfontstyle, 0, false);
		} else {
			$this->SetFont($this->currentfontfamily, $this->currentfontstyle, 0, false);
		}

		foreach ($arrayaux as $k => $v) {
			if ($type != 'INLINE' && $tag != 'BODY' && $type != 'TABLECELL') {
				switch ($k) {
					// BORDERS
					case 'BORDER-TOP':
						$this->blk[$this->blklvl]['border_top'] = $this->border_details($v);
						if ($this->blk[$this->blklvl]['border_top']['s']) {
							$this->blk[$this->blklvl]['border'] = 1;
						}
						break;
					case 'BORDER-BOTTOM':
						$this->blk[$this->blklvl]['border_bottom'] = $this->border_details($v);
						if ($this->blk[$this->blklvl]['border_bottom']['s']) {
							$this->blk[$this->blklvl]['border'] = 1;
						}
						break;
					case 'BORDER-LEFT':
						$this->blk[$this->blklvl]['border_left'] = $this->border_details($v);
						if ($this->blk[$this->blklvl]['border_left']['s']) {
							$this->blk[$this->blklvl]['border'] = 1;
						}
						break;
					case 'BORDER-RIGHT':
						$this->blk[$this->blklvl]['border_right'] = $this->border_details($v);
						if ($this->blk[$this->blklvl]['border_right']['s']) {
							$this->blk[$this->blklvl]['border'] = 1;
						}
						break;

					// PADDING
					case 'PADDING-TOP':
						$this->blk[$this->blklvl]['padding_top'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'PADDING-BOTTOM':
						$this->blk[$this->blklvl]['padding_bottom'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'PADDING-LEFT':
						if (($tag == 'UL' || $tag == 'OL') && $v == 'auto') {
							$this->blk[$this->blklvl]['padding_left'] = 'auto';
							break;
						}
						$this->blk[$this->blklvl]['padding_left'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'PADDING-RIGHT':
						if (($tag == 'UL' || $tag == 'OL') && $v == 'auto') {
							$this->blk[$this->blklvl]['padding_right'] = 'auto';
							break;
						}
						$this->blk[$this->blklvl]['padding_right'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;

					// MARGINS
					case 'MARGIN-TOP':
						$tmp = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						if (isset($this->blk[$this->blklvl]['lastbottommargin'])) {
							if ($tmp > $this->blk[$this->blklvl]['lastbottommargin']) {
								$tmp -= $this->blk[$this->blklvl]['lastbottommargin'];
							} else {
								$tmp = 0;
							}
						}
						$this->blk[$this->blklvl]['margin_top'] = $tmp;
						break;
					case 'MARGIN-BOTTOM':
						$this->blk[$this->blklvl]['margin_bottom'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'MARGIN-LEFT':
						$this->blk[$this->blklvl]['margin_left'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'MARGIN-RIGHT':
						$this->blk[$this->blklvl]['margin_right'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;

					/* -- BORDER-RADIUS -- */
					case 'BORDER-TOP-LEFT-RADIUS-H':
						$this->blk[$this->blklvl]['border_radius_TL_H'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-TOP-LEFT-RADIUS-V':
						$this->blk[$this->blklvl]['border_radius_TL_V'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-TOP-RIGHT-RADIUS-H':
						$this->blk[$this->blklvl]['border_radius_TR_H'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-TOP-RIGHT-RADIUS-V':
						$this->blk[$this->blklvl]['border_radius_TR_V'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-BOTTOM-LEFT-RADIUS-H':
						$this->blk[$this->blklvl]['border_radius_BL_H'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-BOTTOM-LEFT-RADIUS-V':
						$this->blk[$this->blklvl]['border_radius_BL_V'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-BOTTOM-RIGHT-RADIUS-H':
						$this->blk[$this->blklvl]['border_radius_BR_H'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					case 'BORDER-BOTTOM-RIGHT-RADIUS-V':
						$this->blk[$this->blklvl]['border_radius_BR_V'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						break;
					/* -- END BORDER-RADIUS -- */

					case 'BOX-SHADOW':
						$bs = $this->cssmgr->setCSSboxshadow($v);
						if ($bs) {
							$this->blk[$this->blklvl]['box_shadow'] = $bs;
						}
						break;

					case 'BACKGROUND-CLIP':
						if (strtoupper($v) == 'PADDING-BOX') {
							$this->blk[$this->blklvl]['background_clip'] = 'padding-box';
						} else if (strtoupper($v) == 'CONTENT-BOX') {
							$this->blk[$this->blklvl]['background_clip'] = 'content-box';
						}
						break;

					case 'PAGE-BREAK-AFTER':
						if (strtoupper($v) == 'AVOID') {
							$this->blk[$this->blklvl]['page_break_after_avoid'] = true;
						} else if (strtoupper($v) == 'ALWAYS' || strtoupper($v) == 'LEFT' || strtoupper($v) == 'RIGHT') {
							$this->blk[$this->blklvl]['page_break_after'] = strtoupper($v);
						}
						break;

					// mPDF 6 pagebreaktype
					case 'BOX-DECORATION-BREAK':
						if (strtoupper($v) == 'CLONE') {
							$this->blk[$this->blklvl]['box_decoration_break'] = 'clone';
						} else if (strtoupper($v) == 'SLICE') {
							$this->blk[$this->blklvl]['box_decoration_break'] = 'slice';
						}
						break;

					case 'WIDTH':
						if (strtoupper($v) != 'AUTO') {
							$this->blk[$this->blklvl]['css_set_width'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false);
						}
						break;

					// mPDF 6  Lists
					// LISTS
					case 'LIST-STYLE-TYPE':
						$this->blk[$this->blklvl]['list_style_type'] = strtolower($v);
						break;
					case 'LIST-STYLE-IMAGE':
						$this->blk[$this->blklvl]['list_style_image'] = strtolower($v);
						break;
					case 'LIST-STYLE-POSITION':
						$this->blk[$this->blklvl]['list_style_position'] = strtolower($v);
						break;
				}//end of switch($k)
			}


			if ($type != 'INLINE' && $type != 'TABLECELL') { // All block-level, including BODY tag
				switch ($k) {

					case 'TEXT-INDENT':
						// Computed value - to inherit
						$this->blk[$this->blklvl]['text_indent'] = $this->ConvertSize($v, $this->blk[$prevlevel]['inner_width'], $this->FontSize, false) . 'mm';
						break;

					case 'MARGIN-COLLAPSE': // Custom tag to collapse margins at top and bottom of page
						if (strtoupper($v) == 'COLLAPSE') {
							$this->blk[$this->blklvl]['margin_collapse'] = true;
						}
						break;

					case 'LINE-HEIGHT':
						$this->blk[$this->blklvl]['line_height'] = $this->fixLineheight($v);
						if (!$this->blk[$this->blklvl]['line_height']) {
							$this->blk[$this->blklvl]['line_height'] = 'N';
						} // mPDF 6
						break;

					// mPDF 6
					case 'LINE-STACKING-STRATEGY':
						$this->blk[$this->blklvl]['line_stacking_strategy'] = strtolower($v);
						break;

					case 'LINE-STACKING-SHIFT':
						$this->blk[$this->blklvl]['line_stacking_shift'] = strtolower($v);
						break;

					case 'TEXT-ALIGN': //left right center justify
						switch (strtoupper($v)) {
							case 'LEFT':
								$this->blk[$this->blklvl]['align'] = "L";
								break;
							case 'CENTER':
								$this->blk[$this->blklvl]['align'] = "C";
								break;
							case 'RIGHT':
								$this->blk[$this->blklvl]['align'] = "R";
								break;
							case 'JUSTIFY':
								$this->blk[$this->blklvl]['align'] = "J";
								break;
						}
						break;

					/* -- BACKGROUNDS -- */
					case 'BACKGROUND-GRADIENT':
						if ($type == 'BLOCK') {
							$this->blk[$this->blklvl]['gradient'] = $v;
						}
						break;
					/* -- END BACKGROUNDS -- */

					case 'DIRECTION':
						if ($v) {
							$this->blk[$this->blklvl]['direction'] = strtolower($v);
						}
						break;
				}//end of switch($k)
			}

			// FOR INLINE ONLY
			if ($type == 'INLINE') {
				switch ($k) {
					case 'DISPLAY':
						if (strtoupper($v) == 'NONE') {
							$this->inlineDisplayOff = true;
						}
						break;
					case 'DIRECTION':
						break;
				}//end of switch($k)
			}
			// FOR INLINE ONLY
			if ($type == 'INLINE') {
				switch ($k) {
					// BORDERS
					case 'BORDER-TOP':
						$this->spanborddet['T'] = $this->border_details($v);
						$this->spanborder = true;
						$spanbordset = true;
						break;
					case 'BORDER-BOTTOM':
						$this->spanborddet['B'] = $this->border_details($v);
						$this->spanborder = true;
						$spanbordset = true;
						break;
					case 'BORDER-LEFT':
						$this->spanborddet['L'] = $this->border_details($v);
						$this->spanborder = true;
						$spanbordset = true;
						break;
					case 'BORDER-RIGHT':
						$this->spanborddet['R'] = $this->border_details($v);
						$this->spanborder = true;
						$spanbordset = true;
						break;
					case 'VISIBILITY': // block is set in OpenTag
						$v = strtolower($v);
						if ($v == 'visible' || $v == 'hidden' || $v == 'printonly' || $v == 'screenonly') {
							$this->textparam['visibility'] = $v;
						}
						break;
				}//end of switch($k)
			}

			if ($type != 'TABLECELL') {
				// FOR INLINE and BLOCK
				switch ($k) {
					case 'TEXT-ALIGN': //left right center justify
						if (strtoupper($v) == 'NOJUSTIFY' && $this->blk[$this->blklvl]['align'] == "J") {
							$this->blk[$this->blklvl]['align'] = "";
						}
						break;
					// bgcolor only - to stay consistent with original html2fpdf
					case 'BACKGROUND':
					case 'BACKGROUND-COLOR':
						$cor = $this->ConvertColor($v);
						if ($cor) {
							if ($tag == 'BODY') {
								$this->bodyBackgroundColor = $cor;
							} else if ($type == 'INLINE') {
								$this->spanbgcolorarray = $cor;
								$this->spanbgcolor = true;
								$spanbgset = true;
							} else {
								$this->blk[$this->blklvl]['bgcolorarray'] = $cor;
								$this->blk[$this->blklvl]['bgcolor'] = true;
							}
						} else if ($type != 'INLINE') {
							if ($this->ColActive) {
								$this->blk[$this->blklvl]['bgcolorarray'] = $this->blk[$prevlevel]['bgcolorarray'];
								$this->blk[$this->blklvl]['bgcolor'] = $this->blk[$prevlevel]['bgcolor'];
							}
						}
						break;

					case 'VERTICAL-ALIGN': //super and sub only dealt with here e.g. <SUB> and <SUP>
						switch (strtoupper($v)) {
							case 'SUPER':
								$this->textvar = ($this->textvar | FA_SUPERSCRIPT); // mPDF 5.7.1
								$this->textvar = ($this->textvar & ~FA_SUBSCRIPT);
								// mPDF 5.7.3  inline text-decoration parameters
								if (isset($this->textparam['text-baseline'])) {
									$this->textparam['text-baseline'] += ($this->baselineSup) * $preceeding_fontsize;
								} else {
									$this->textparam['text-baseline'] = ($this->baselineSup) * $preceeding_fontsize;
								}
								break;
							case 'SUB':
								$this->textvar = ($this->textvar | FA_SUBSCRIPT);
								$this->textvar = ($this->textvar & ~FA_SUPERSCRIPT);
								// mPDF 5.7.3  inline text-decoration parameters
								if (isset($this->textparam['text-baseline'])) {
									$this->textparam['text-baseline'] += ($this->baselineSub) * $preceeding_fontsize;
								} else {
									$this->textparam['text-baseline'] = ($this->baselineSub) * $preceeding_fontsize;
								}
								break;
							case 'BASELINE':
								$this->textvar = ($this->textvar & ~FA_SUBSCRIPT);
								$this->textvar = ($this->textvar & ~FA_SUPERSCRIPT);
								// mPDF 5.7.3  inline text-decoration parameters
								if (isset($this->textparam['text-baseline'])) {
									unset($this->textparam['text-baseline']);
								}
								break;
							// mPDF 5.7.3  inline text-decoration parameters
							default:
								$lh = $this->_computeLineheight($this->blk[$this->blklvl]['line_height']);
								$sz = $this->ConvertSize($v, $lh, $this->FontSize, false);
								$this->textvar = ($this->textvar & ~FA_SUBSCRIPT);
								$this->textvar = ($this->textvar & ~FA_SUPERSCRIPT);
								if ($sz) {
									if ($sz > 0) {
										$this->textvar = ($this->textvar | FA_SUPERSCRIPT);
									} else {
										$this->textvar = ($this->textvar | FA_SUBSCRIPT);
									}
									if (isset($this->textparam['text-baseline'])) {
										$this->textparam['text-baseline'] += $sz;
									} else {
										$this->textparam['text-baseline'] = $sz;
									}
								}
						}
						break;
				}//end of switch($k)
			}


			// FOR ALL
			switch ($k) {
				case 'LETTER-SPACING':
					$this->lSpacingCSS = $v;
					if (($this->lSpacingCSS || $this->lSpacingCSS === '0') && strtoupper($this->lSpacingCSS) != 'NORMAL') {
						$this->fixedlSpacing = $this->ConvertSize($this->lSpacingCSS, $this->FontSize);
					}
					break;

				case 'WORD-SPACING':
					$this->wSpacingCSS = $v;
					if ($this->wSpacingCSS && strtoupper($this->wSpacingCSS) != 'NORMAL') {
						$this->minwSpacing = $this->ConvertSize($this->wSpacingCSS, $this->FontSize);
					}
					break;

				case 'FONT-STYLE': // italic normal oblique
					switch (strtoupper($v)) {
						case 'ITALIC':
						case 'OBLIQUE':
							$this->SetStyle('I', true);
							break;
						case 'NORMAL':
							$this->SetStyle('I', false);
							break;
					}
					break;

				case 'FONT-WEIGHT': // normal bold //Does not support: bolder, lighter, 100..900(step value=100)
					switch (strtoupper($v)) {
						case 'BOLD':
							$this->SetStyle('B', true);
							break;
						case 'NORMAL':
							$this->SetStyle('B', false);
							break;
					}
					break;

				case 'FONT-KERNING':
					if (strtoupper($v) == 'NORMAL' || (strtoupper($v) == 'AUTO' && $this->useKerning)) {
						/* -- OTL -- */
						if ($this->CurrentFont['haskernGPOS']) {
							if (isset($this->OTLtags['Plus'])) {
								$this->OTLtags['Plus'] .= ' kern';
							} else {
								$this->OTLtags['Plus'] = ' kern';
							}
						}
						/* -- END OTL -- */ else {  // *OTL*
							$this->textvar = ($this->textvar | FC_KERNING);
						} // *OTL*
					} else if (strtoupper($v) == 'NONE' || (strtoupper($v) == 'AUTO' && !$this->useKerning)) {
						if (isset($this->OTLtags['Plus']))
							$this->OTLtags['Plus'] = str_replace('kern', '', $this->OTLtags['Plus']); // *OTL*
						if (isset($this->OTLtags['FFPlus']))
							$this->OTLtags['FFPlus'] = preg_replace('/kern[\d]*/', '', $this->OTLtags['FFPlus']);
						$this->textvar = ($this->textvar & ~FC_KERNING);
					}
					break;

				/* -- OTL -- */
				case 'FONT-LANGUAGE-OVERRIDE':
					$v = strtoupper($v);
					if (strpos($v, 'NORMAL') !== false) {
						$this->fontLanguageOverride = '';
					} else {
						$this->fontLanguageOverride = trim($v);
					}
					break;


				case 'FONT-VARIANT-POSITION':
					if (isset($this->OTLtags['Plus']))
						$this->OTLtags['Plus'] = str_replace(array('sups', 'subs'), '', $this->OTLtags['Plus']);
					switch (strtoupper($v)) {
						case 'SUPER':
							$this->OTLtags['Plus'] .= ' sups';
							break;
						case 'SUB':
							$this->OTLtags['Plus'] .= ' subs';
							break;
						case 'NORMAL':
							break;
					}
					break;

				case 'FONT-VARIANT-CAPS':
					$v = strtoupper($v);
					if (!isset($this->OTLtags['Plus'])) {
						$this->OTLtags['Plus'] = '';
					}
					$this->OTLtags['Plus'] = str_replace(array('c2sc', 'smcp', 'c2pc', 'pcap', 'unic', 'titl'), '', $this->OTLtags['Plus']);
					$this->textvar = ($this->textvar & ~FC_SMALLCAPS);   // ?????????????? <small-caps>
					if (strpos($v, 'ALL-SMALL-CAPS') !== false) {
						$this->OTLtags['Plus'] .= ' c2sc smcp';
					} else if (strpos($v, 'SMALL-CAPS') !== false) {
						if (isset($this->CurrentFont['hassmallcapsGSUB']) && $this->CurrentFont['hassmallcapsGSUB']) {
							$this->OTLtags['Plus'] .= ' smcp';
						} else {
							$this->textvar = ($this->textvar | FC_SMALLCAPS);
						}
					} else if (strpos($v, 'ALL-PETITE-CAPS') !== false) {
						$this->OTLtags['Plus'] .= ' c2pc pcap';
					} else if (strpos($v, 'PETITE-CAPS') !== false) {
						$this->OTLtags['Plus'] .= ' pcap';
					} else if (strpos($v, 'UNICASE') !== false) {
						$this->OTLtags['Plus'] .= ' unic';
					} else if (strpos($v, 'TITLING-CAPS') !== false) {
						$this->OTLtags['Plus'] .= ' titl';
					}
					break;

				case 'FONT-VARIANT-LIGATURES':
					$v = strtoupper($v);
					if (!isset($this->OTLtags['Plus'])) {
						$this->OTLtags['Plus'] = '';
					}
					if (!isset($this->OTLtags['Minus'])) {
						$this->OTLtags['Minus'] = '';
					}
					if (strpos($v, 'NORMAL') !== false) {
						$this->OTLtags['Minus'] = str_replace(array('liga', 'clig', 'calt'), '', $this->OTLtags['Minus']);
						$this->OTLtags['Plus'] = str_replace(array('dlig', 'hlig'), '', $this->OTLtags['Plus']);
					} else if (strpos($v, 'NONE') !== false) {
						$this->OTLtags['Minus'] .= ' liga clig calt';
						$this->OTLtags['Plus'] = str_replace(array('dlig', 'hlig'), '', $this->OTLtags['Plus']);
					}
					if (strpos($v, 'NO-COMMON-LIGATURES') !== false) {
						$this->OTLtags['Minus'] .= ' liga clig';
					} else if (strpos($v, 'COMMON-LIGATURES') !== false) {
						$this->OTLtags['Minus'] = str_replace(array('liga', 'clig'), '', $this->OTLtags['Minus']);
					}
					if (strpos($v, 'NO-CONTEXTUAL') !== false) {
						$this->OTLtags['Minus'] .= ' calt';
					} else if (strpos($v, 'CONTEXTUAL') !== false) {
						$this->OTLtags['Minus'] = str_replace('calt', '', $this->OTLtags['Minus']);
					}
					if (strpos($v, 'NO-DISCRETIONARY-LIGATURES') !== false) {
						$this->OTLtags['Plus'] = str_replace('dlig', '', $this->OTLtags['Plus']);
					} else if (strpos($v, 'DISCRETIONARY-LIGATURES') !== false) {
						$this->OTLtags['Plus'] .= ' dlig';
					}
					if (strpos($v, 'NO-HISTORICAL-LIGATURES') !== false) {
						$this->OTLtags['Plus'] = str_replace('hlig', '', $this->OTLtags['Plus']);
					} else if (strpos($v, 'HISTORICAL-LIGATURES') !== false) {
						$this->OTLtags['Plus'] .= ' hlig';
					}

					break;

				case 'FONT-VARIANT-NUMERIC':
					$v = strtoupper($v);
					if (!isset($this->OTLtags['Plus'])) {
						$this->OTLtags['Plus'] = '';
					}
					if (strpos($v, 'NORMAL') !== false) {
						$this->OTLtags['Plus'] = str_replace(array('ordn', 'zero', 'lnum', 'onum', 'pnum', 'tnum', 'frac', 'afrc'), '', $this->OTLtags['Plus']);
					}
					if (strpos($v, 'ORDINAL') !== false) {
						$this->OTLtags['Plus'] .= ' ordn';
					}
					if (strpos($v, 'SLASHED-ZERO') !== false) {
						$this->OTLtags['Plus'] .= ' zero';
					}
					if (strpos($v, 'LINING-NUMS') !== false) {
						$this->OTLtags['Plus'] .= ' lnum';
						$this->OTLtags['Plus'] = str_replace('onum', '', $this->OTLtags['Plus']);
					} else if (strpos($v, 'OLDSTYLE-NUMS') !== false) {
						$this->OTLtags['Plus'] .= ' onum';
						$this->OTLtags['Plus'] = str_replace('lnum', '', $this->OTLtags['Plus']);
					}
					if (strpos($v, 'PROPORTIONAL-NUMS') !== false) {
						$this->OTLtags['Plus'] .= ' pnum';
						$this->OTLtags['Plus'] = str_replace('tnum', '', $this->OTLtags['Plus']);
					} else if (strpos($v, 'TABULAR-NUMS') !== false) {
						$this->OTLtags['Plus'] .= ' tnum';
						$this->OTLtags['Plus'] = str_replace('pnum', '', $this->OTLtags['Plus']);
					}
					if (strpos($v, 'DIAGONAL-FRACTIONS') !== false) {
						$this->OTLtags['Plus'] .= ' frac';
						$this->OTLtags['Plus'] = str_replace('afrc', '', $this->OTLtags['Plus']);
					} else if (strpos($v, 'STACKED-FRACTIONS') !== false) {
						$this->OTLtags['Plus'] .= ' afrc';
						$this->OTLtags['Plus'] = str_replace('frac', '', $this->OTLtags['Plus']);
					}
					break;

				case 'FONT-VARIANT-ALTERNATES':  // Only supports historical-forms
					$v = strtoupper($v);
					if (!isset($this->OTLtags['Plus'])) {
						$this->OTLtags['Plus'] = '';
					}
					if (strpos($v, 'NORMAL') !== false) {
						$this->OTLtags['Plus'] = str_replace('hist', '', $this->OTLtags['Plus']);
					}
					if (strpos($v, 'HISTORICAL-FORMS') !== false) {
						$this->OTLtags['Plus'] .= ' hist';
					}
					break;


				case 'FONT-FEATURE-SETTINGS':
					$v = strtolower($v);
					if (strpos($v, 'normal') !== false) {
						$this->OTLtags['FFMinus'] = '';
						$this->OTLtags['FFPlus'] = '';
					} else {
						if (!isset($this->OTLtags['FFPlus'])) {
							$this->OTLtags['FFPlus'] = '';
						}
						if (!isset($this->OTLtags['FFMinus'])) {
							$this->OTLtags['FFMinus'] = '';
						}
						$tags = preg_split('/[,]/', $v);
						foreach ($tags AS $t) {
							if (preg_match('/[\"\']([a-zA-Z0-9]{4})[\"\']\s*(on|off|\d*){0,1}/', $t, $m)) {
								if ($m[2] == 'off' || $m[2] === '0') {
									if (strpos($this->OTLtags['FFMinus'], $m[1]) === false) {
										$this->OTLtags['FFMinus'] .= ' ' . $m[1];
									}
									$this->OTLtags['FFPlus'] = preg_replace('/' . $m[1] . '[\d]*/', '', $this->OTLtags['FFPlus']);
								} else {
									if ($m[2] == 'on') {
										$m[2] = '1';
									}
									if (strpos($this->OTLtags['FFPlus'], $m[1]) === false) {
										$this->OTLtags['FFPlus'] .= ' ' . $m[1] . $m[2];
									}
									$this->OTLtags['FFMinus'] = str_replace($m[1], '', $this->OTLtags['FFMinus']);
								}
							}
						}
					}
					break;
				/* -- END OTL -- */


				case 'TEXT-TRANSFORM': // none uppercase lowercase //Does support: capitalize
					switch (strtoupper($v)) { //Not working 100%
						case 'CAPITALIZE':
							$this->textvar = ($this->textvar | FT_CAPITALIZE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_UPPERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_LOWERCASE); // mPDF 5.7.1
							break;
						case 'UPPERCASE':
							$this->textvar = ($this->textvar | FT_UPPERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_LOWERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_CAPITALIZE); // mPDF 5.7.1
							break;
						case 'LOWERCASE':
							$this->textvar = ($this->textvar | FT_LOWERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_UPPERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_CAPITALIZE); // mPDF 5.7.1
							break;
						case 'NONE': break;
							$this->textvar = ($this->textvar & ~FT_UPPERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_LOWERCASE); // mPDF 5.7.1
							$this->textvar = ($this->textvar & ~FT_CAPITALIZE); // mPDF 5.7.1
					}
					break;

				case 'TEXT-SHADOW':
					$ts = $this->cssmgr->setCSStextshadow($v);
					if ($ts) {
						$this->textshadow = $ts;
					}
					break;

				case 'HYPHENS':
					if (strtoupper($v) == 'NONE') {
						$this->textparam['hyphens'] = 2;
					} else if (strtoupper($v) == 'AUTO') {
						$this->textparam['hyphens'] = 1;
					} else if (strtoupper($v) == 'MANUAL') {
						$this->textparam['hyphens'] = 0;
					}
					break;

				case 'TEXT-OUTLINE':
					if (strtoupper($v) == 'NONE') {
						$this->textparam['outline-s'] = false;
					}
					break;

				case 'TEXT-OUTLINE-WIDTH':
				case 'OUTLINE-WIDTH':
					switch (strtoupper($v)) {
						case 'THIN': $v = '0.03em';
							break;
						case 'MEDIUM': $v = '0.05em';
							break;
						case 'THICK': $v = '0.07em';
							break;
					}
					$w = $this->ConvertSize($v, $this->FontSize, $this->FontSize);
					if ($w) {
						$this->textparam['outline-WIDTH'] = $w;
						$this->textparam['outline-s'] = true;
					} else {
						$this->textparam['outline-s'] = false;
					}
					break;

				case 'TEXT-OUTLINE-COLOR':
				case 'OUTLINE-COLOR':
					if (strtoupper($v) == 'INVERT') {
						if ($this->colorarray) {
							$cor = $this->colorarray;
							$this->textparam['outline-COLOR'] = $this->_invertColor($cor);
						} else {
							$this->textparam['outline-COLOR'] = $this->ConvertColor(255);
						}
					} else {
						$cor = $this->ConvertColor($v);
						if ($cor) {
							$this->textparam['outline-COLOR'] = $cor;
						}
					}
					break;

				case 'COLOR': // font color
					$cor = $this->ConvertColor($v);
					if ($cor) {
						$this->colorarray = $cor;
						$this->SetTColor($cor);
					}
					break;
			}//end of switch($k)
		}//end of foreach
		// mPDF 5.7.3  inline text-decoration parameters
		// Needs to be set at the end - after vertical-align = super/sub, so that textparam['text-baseline'] is set
		if (isset($arrayaux['TEXT-DECORATION'])) {
			$v = $arrayaux['TEXT-DECORATION']; // none underline line-through (strikeout) //Does not support: blink
			if (stristr($v, 'LINE-THROUGH')) {
				$this->textvar = ($this->textvar | FD_LINETHROUGH);
				// mPDF 5.7.3  inline text-decoration parameters
				if (isset($this->textparam['text-baseline'])) {
					$this->textparam['s-decoration']['baseline'] = $this->textparam['text-baseline'];
				} else {
					$this->textparam['s-decoration']['baseline'] = 0;
				}
				$this->textparam['s-decoration']['fontkey'] = $this->FontFamily . $this->FontStyle;
				$this->textparam['s-decoration']['fontsize'] = $this->FontSize;
				$this->textparam['s-decoration']['color'] = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			}
			if (stristr($v, 'UNDERLINE')) {
				$this->textvar = ($this->textvar | FD_UNDERLINE);
				// mPDF 5.7.3  inline text-decoration parameters
				if (isset($this->textparam['text-baseline'])) {
					$this->textparam['u-decoration']['baseline'] = $this->textparam['text-baseline'];
				} else {
					$this->textparam['u-decoration']['baseline'] = 0;
				}
				$this->textparam['u-decoration']['fontkey'] = $this->FontFamily . $this->FontStyle;
				$this->textparam['u-decoration']['fontsize'] = $this->FontSize;
				$this->textparam['u-decoration']['color'] = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			}
			if (stristr($v, 'OVERLINE')) {
				$this->textvar = ($this->textvar | FD_OVERLINE);
				// mPDF 5.7.3  inline text-decoration parameters
				if (isset($this->textparam['text-baseline'])) {
					$this->textparam['o-decoration']['baseline'] = $this->textparam['text-baseline'];
				} else {
					$this->textparam['o-decoration']['baseline'] = 0;
				}
				$this->textparam['o-decoration']['fontkey'] = $this->FontFamily . $this->FontStyle;
				$this->textparam['o-decoration']['fontsize'] = $this->FontSize;
				$this->textparam['o-decoration']['color'] = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			}
			if (stristr($v, 'NONE')) {
				$this->textvar = ($this->textvar & ~FD_UNDERLINE);
				$this->textvar = ($this->textvar & ~FD_LINETHROUGH);
				$this->textvar = ($this->textvar & ~FD_OVERLINE);
				// mPDF 5.7.3  inline text-decoration parameters
				if (isset($this->textparam['u-decoration'])) {
					unset($this->textparam['u-decoration']);
				}
				if (isset($this->textparam['s-decoration'])) {
					unset($this->textparam['s-decoration']);
				}
				if (isset($this->textparam['o-decoration'])) {
					unset($this->textparam['o-decoration']);
				}
			}
		}
		// mPDF 6
		if ($spanbordset) { // BORDER has been set on this INLINE element
			if (isset($this->textparam['text-baseline'])) {
				$this->textparam['bord-decoration']['baseline'] = $this->textparam['text-baseline'];
			} else {
				$this->textparam['bord-decoration']['baseline'] = 0;
			}
			$this->textparam['bord-decoration']['fontkey'] = $this->FontFamily . $this->FontStyle;
			$this->textparam['bord-decoration']['fontsize'] = $this->FontSize;
		}
		if ($spanbgset) { // BACKGROUND[-COLOR] has been set on this INLINE element
			if (isset($this->textparam['text-baseline'])) {
				$this->textparam['bg-decoration']['baseline'] = $this->textparam['text-baseline'];
			} else {
				$this->textparam['bg-decoration']['baseline'] = 0;
			}
			$this->textparam['bg-decoration']['fontkey'] = $this->FontFamily . $this->FontStyle;
			$this->textparam['bg-decoration']['fontsize'] = $this->FontSize;
		}
	}

	/* -- END HTML-CSS -- */

	function SetStyle($tag, $enable)
	{
		$this->$tag = $enable;
		$style = '';
		foreach (array('B', 'I') as $s) {
			if ($this->$s) {
				$style.=$s;
			}
		}
		$this->currentfontstyle = $style;
		$this->SetFont('', $style, 0, false);
	}

// Set multiple styles at one time
	function SetStylesArray($arr)
	{
		$style = '';
		foreach (array('B', 'I') as $s) {
			if (isset($arr[$s])) {
				if ($arr[$s]) {
					$this->$s = true;
					$style.=$s;
				} else {
					$this->$s = false;
				}
			} else if ($this->$s) {
				$style.=$s;
			}
		}
		$this->currentfontstyle = $style;
		$this->SetFont('', $style, 0, false);
	}

// Set multiple styles at one $str e.g. "BI"
	function SetStyles($str)
	{
		$style = '';
		foreach (array('B', 'I') as $s) {
			if (strpos($str, $s) !== false) {
				$this->$s = true;
				$style.=$s;
			} else {
				$this->$s = false;
			}
		}
		$this->currentfontstyle = $style;
		$this->SetFont('', $style, 0, false);
	}

	function ResetStyles()
	{
		foreach (array('B', 'I') as $s) {
			$this->$s = false;
		}
		$this->currentfontstyle = '';
		$this->SetFont('', '', 0, false);
	}

	function DisableTags($str = '')
	{
		if ($str == '') { //enable all tags
			//Insert new supported tags in the long string below.
			$this->enabledtags = "<a><acronym><address><article><aside><b><bdi><bdo><big><blockquote><br><caption><center><cite><code><del><details><dd><div><dl><dt><em><fieldset><figcaption><figure><font><form><h1><h2><h3><h4><h5><h6><hgroup><hr><i><img><input><ins><kbd><legend><li><main><mark><meter><nav><ol><option><p><pre><progress><q><s><samp><section><select><small><span><strike><strong><sub><summary><sup><table><tbody><td><template><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><footer><header><annotation><bookmark><jpgraph><textcircle><barcode><dottab><indexentry><indexinsert><watermarktext><watermarkimage><tts><ttz><tta><column_break><columnbreak><newcolumn><newpage><page_break><pagebreak><formfeed><columns><toc><tocentry><tocpagebreak><pageheader><pagefooter><setpageheader><setpagefooter><sethtmlpageheader><sethtmlpagefooter>";
		} else {
			$str = explode(",", $str);
			foreach ($str as $v)
				$this->enabledtags = str_replace(trim($v), '', $this->enabledtags);
		}
	}



	function read_short(&$fh)
	{
		$s = fread($fh, 2);
		$a = (ord($s[0]) << 8) + ord($s[1]);
		if ($a & (1 << 15)) {
			$a = ($a - (1 << 16));
		}
		return $a;
	}


	function _putextgstates()
	{
		for ($i = 1; $i <= count($this->extgstates); $i++) {
			$this->_newobj();
			$this->extgstates[$i]['n'] = $this->n;
			$this->_out('<</Type /ExtGState');
			foreach ($this->extgstates[$i]['parms'] as $k => $v)
				$this->_out('/' . $k . ' ' . $v);
			$this->_out('>>');
			$this->_out('endobj');
		}
	}

	function _putocg()
	{
		if ($this->hasOC) {
			$this->_newobj();
			$this->n_ocg_print = $this->n;
			$this->_out('<</Type /OCG /Name ' . $this->_textstring('Print only'));
			$this->_out('/Usage <</Print <</PrintState /ON>> /View <</ViewState /OFF>>>>>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->n_ocg_view = $this->n;
			$this->_out('<</Type /OCG /Name ' . $this->_textstring('Screen only'));
			$this->_out('/Usage <</Print <</PrintState /OFF>> /View <</ViewState /ON>>>>>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->n_ocg_hidden = $this->n;
			$this->_out('<</Type /OCG /Name ' . $this->_textstring('Hidden'));
			$this->_out('/Usage <</Print <</PrintState /OFF>> /View <</ViewState /OFF>>>>>>');
			$this->_out('endobj');
		}
		if (count($this->layers)) {
			ksort($this->layers);
			foreach ($this->layers as $id => $layer) {
				$this->_newobj();
				$this->layers[$id]['n'] = $this->n;
				if (isset($this->layerDetails[$id]['name']) && $this->layerDetails[$id]['name']) {
					$name = $this->layerDetails[$id]['name'];
				} else {
					$name = $layer['name'];
				}
				$this->_out('<</Type /OCG /Name ' . $this->_UTF16BEtextstring($name) . '>>');
				$this->_out('endobj');
			}
		}
	}


	function _putpatterns()
	{
		for ($i = 1; $i <= count($this->patterns); $i++) {
			$x = $this->patterns[$i]['x'];
			$y = $this->patterns[$i]['y'];
			$w = $this->patterns[$i]['w'];
			$h = $this->patterns[$i]['h'];
			$pgh = $this->patterns[$i]['pgh'];
			$orig_w = $this->patterns[$i]['orig_w'];
			$orig_h = $this->patterns[$i]['orig_h'];
			$image_id = $this->patterns[$i]['image_id'];
			$itype = $this->patterns[$i]['itype'];
			if (isset($this->patterns[$i]['bpa'])) {
				$bpa = $this->patterns[$i]['bpa'];
			} // background positioning area
			else {
				$bpa = array();
			}

			if ($this->patterns[$i]['x_repeat']) {
				$x_repeat = true;
			} else {
				$x_repeat = false;
			}
			if ($this->patterns[$i]['y_repeat']) {
				$y_repeat = true;
			} else {
				$y_repeat = false;
			}
			$x_pos = $this->patterns[$i]['x_pos'];
			if (stristr($x_pos, '%')) {
				$x_pos += 0;
				$x_pos /= 100;
				if (isset($bpa['w']) && $bpa['w'])
					$x_pos = ($bpa['w'] * $x_pos) - ($orig_w / _MPDFK * $x_pos);
				else
					$x_pos = ($w * $x_pos) - ($orig_w / _MPDFK * $x_pos);
			}
			$y_pos = $this->patterns[$i]['y_pos'];
			if (stristr($y_pos, '%')) {
				$y_pos += 0;
				$y_pos /= 100;
				if (isset($bpa['h']) && $bpa['h'])
					$y_pos = ($bpa['h'] * $y_pos) - ($orig_h / _MPDFK * $y_pos);
				else
					$y_pos = ($h * $y_pos) - ($orig_h / _MPDFK * $y_pos);
			}
			if (isset($bpa['x']) && $bpa['x'])
				$adj_x = ($x_pos + $bpa['x']) * _MPDFK;
			else
				$adj_x = ($x_pos + $x) * _MPDFK;
			if (isset($bpa['y']) && $bpa['y'])
				$adj_y = (($pgh - $y_pos - $bpa['y']) * _MPDFK) - $orig_h;
			else
				$adj_y = (($pgh - $y_pos - $y) * _MPDFK) - $orig_h;
			$img_obj = false;
			if ($itype == 'svg' || $itype == 'wmf') {
				foreach ($this->formobjects AS $fo) {
					if ($fo['i'] == $image_id) {
						$img_obj = $fo['n'];
						$fo_w = $fo['w'];
						$fo_h = -$fo['h'];
						$wmf_x = $fo['x'];
						$wmf_y = $fo['y'];
						break;
					}
				}
			} else {
				foreach ($this->images AS $img) {
					if ($img['i'] == $image_id) {
						$img_obj = $img['n'];
						break;
					}
				}
			}
			if (!$img_obj) {
				echo "Problem: Image object not found for background pattern " . $img['i'];
				exit;
			}

			$this->_newobj();
			$this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
			if ($itype == 'svg' || $itype == 'wmf') {
				$this->_out('/XObject <</FO' . $image_id . ' ' . $img_obj . ' 0 R >>');
				// ******* ADD ANY ExtGStates, Shading AND Fonts needed for the FormObject
				// Set in classes/svg array['fo'] = true
				// Required that _putshaders comes before _putpatterns in _putresources
				// This adds any resources associated with any FormObject to every Formobject - overkill but works!
				if (count($this->extgstates)) {
					$this->_out('/ExtGState <<');
					foreach ($this->extgstates as $k => $extgstate)
						if (isset($extgstate['fo']) && $extgstate['fo']) {
							if (isset($extgstate['trans']))
								$this->_out('/' . $extgstate['trans'] . ' ' . $extgstate['n'] . ' 0 R');
							else
								$this->_out('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
						}
					$this->_out('>>');
				}
				/* -- BACKGROUNDS -- */
				if (isset($this->gradients) AND ( count($this->gradients) > 0)) {
					$this->_out('/Shading <<');
					foreach ($this->gradients as $id => $grad) {
						if (isset($grad['fo']) && $grad['fo']) {
							$this->_out('/Sh' . $id . ' ' . $grad['id'] . ' 0 R');
						}
					}
					$this->_out('>>');
				}
				/* -- END BACKGROUNDS -- */
				$this->_out('/Font <<');
				foreach ($this->fonts as $font) {
					if (!$font['used'] && $font['type'] == 'TTF') {
						continue;
					}
					if (isset($font['fo']) && $font['fo']) {
						if ($font['type'] == 'TTF' && ($font['sip'] || $font['smp'])) {
							foreach ($font['n'] AS $k => $fid) {
								$this->_out('/F' . $font['subsetfontids'][$k] . ' ' . $font['n'][$k] . ' 0 R');
							}
						} else {
							$this->_out('/F' . $font['i'] . ' ' . $font['n'] . ' 0 R');
						}
					}
				}
				$this->_out('>>');
			} else {
				$this->_out('/XObject <</I' . $image_id . ' ' . $img_obj . ' 0 R >>');
			}
			$this->_out('>>');
			$this->_out('endobj');

			$this->_newobj();
			$this->patterns[$i]['n'] = $this->n;
			$this->_out('<< /Type /Pattern /PatternType 1 /PaintType 1 /TilingType 2');
			$this->_out('/Resources ' . ($this->n - 1) . ' 0 R');

			$this->_out(sprintf('/BBox [0 0 %.3F %.3F]', $orig_w, $orig_h));
			if ($x_repeat) {
				$this->_out(sprintf('/XStep %.3F', $orig_w));
			} else {
				$this->_out(sprintf('/XStep %d', 99999));
			}
			if ($y_repeat) {
				$this->_out(sprintf('/YStep %.3F', $orig_h));
			} else {
				$this->_out(sprintf('/YStep %d', 99999));
			}

			if ($itype == 'svg' || $itype == 'wmf') {
				$this->_out(sprintf('/Matrix [1 0 0 -1 %.3F %.3F]', $adj_x, ($adj_y + $orig_h)));
				$s = sprintf("q %.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q", ($orig_w / $fo_w), (-$orig_h / $fo_h), -($orig_w / $fo_w) * $wmf_x, ($orig_w / $fo_w) * $wmf_y, $image_id);
			} else {
				$this->_out(sprintf('/Matrix [1 0 0 1 %.3F %.3F]', $adj_x, $adj_y));
				$s = sprintf("q %.3F 0 0 %.3F 0 0 cm /I%d Do Q", $orig_w, $orig_h, $image_id);
			}

			if ($this->compress) {
				$this->_out('/Filter /FlateDecode');
				$s = gzcompress($s);
			}
			$this->_out('/Length ' . strlen($s) . '>>');
			$this->_putstream($s);
			$this->_out('endobj');
		}
	}

	/* -- BACKGROUNDS -- */

	function _putshaders()
	{
		$maxid = count($this->gradients); //index for transparency gradients
		foreach ($this->gradients as $id => $grad) {
			if (($grad['type'] == 2 || $grad['type'] == 3) && empty($grad['is_mask'])) {
				$this->_newobj();
				$this->_out('<<');
				$this->_out('/FunctionType 3');
				$this->_out('/Domain [0 1]');
				$fn = array();
				$bd = array();
				$en = array();
				for ($i = 0; $i < (count($grad['stops']) - 1); $i++) {
					$fn[] = ($this->n + 1 + $i) . ' 0 R';
					$en[] = '0 1';
					if ($i > 0) {
						$bd[] = sprintf('%.3F', $grad['stops'][$i]['offset']);
					}
				}
				$this->_out('/Functions [' . implode(' ', $fn) . ']');
				$this->_out('/Bounds [' . implode(' ', $bd) . ']');
				$this->_out('/Encode [' . implode(' ', $en) . ']');
				$this->_out('>>');
				$this->_out('endobj');
				$f1 = $this->n;
				for ($i = 0; $i < (count($grad['stops']) - 1); $i++) {
					$this->_newobj();
					$this->_out('<<');
					$this->_out('/FunctionType 2');
					$this->_out('/Domain [0 1]');
					$this->_out('/C0 [' . $grad['stops'][$i]['col'] . ']');
					$this->_out('/C1 [' . $grad['stops'][$i + 1]['col'] . ']');
					$this->_out('/N 1');
					$this->_out('>>');
					$this->_out('endobj');
				}
			}
			if ($grad['type'] == 2 || $grad['type'] == 3) {
				if (isset($grad['trans']) && $grad['trans']) {
					$this->_newobj();
					$this->_out('<<');
					$this->_out('/FunctionType 3');
					$this->_out('/Domain [0 1]');
					$fn = array();
					$bd = array();
					$en = array();
					for ($i = 0; $i < (count($grad['stops']) - 1); $i++) {
						$fn[] = ($this->n + 1 + $i) . ' 0 R';
						$en[] = '0 1';
						if ($i > 0) {
							$bd[] = sprintf('%.3F', $grad['stops'][$i]['offset']);
						}
					}
					$this->_out('/Functions [' . implode(' ', $fn) . ']');
					$this->_out('/Bounds [' . implode(' ', $bd) . ']');
					$this->_out('/Encode [' . implode(' ', $en) . ']');
					$this->_out('>>');
					$this->_out('endobj');
					$f2 = $this->n;
					for ($i = 0; $i < (count($grad['stops']) - 1); $i++) {
						$this->_newobj();
						$this->_out('<<');
						$this->_out('/FunctionType 2');
						$this->_out('/Domain [0 1]');
						$this->_out(sprintf('/C0 [%.3F]', $grad['stops'][$i]['opacity']));
						$this->_out(sprintf('/C1 [%.3F]', $grad['stops'][$i + 1]['opacity']));
						$this->_out('/N 1');
						$this->_out('>>');
						$this->_out('endobj');
					}
				}
			}

			if (empty($grad['is_mask'])) {
				$this->_newobj();
				$this->_out('<<');
				$this->_out('/ShadingType ' . $grad['type']);
				if (isset($grad['colorspace'])) {
					$this->_out('/ColorSpace /Device' . $grad['colorspace']);  // Can use CMYK if all C0 and C1 above have 4 values
				} else {
					$this->_out('/ColorSpace /DeviceRGB');
				}
				if ($grad['type'] == 2) {
					$this->_out(sprintf('/Coords [%.3F %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3]));
					$this->_out('/Function ' . $f1 . ' 0 R');
					$this->_out('/Extend [' . $grad['extend'][0] . ' ' . $grad['extend'][1] . '] ');
					$this->_out('>>');
				} else if ($grad['type'] == 3) {
					//x0, y0, r0, x1, y1, r1
					//at this this time radius of inner circle is 0
					$ir = 0;
					if (isset($grad['coords'][5]) && $grad['coords'][5]) {
						$ir = $grad['coords'][5];
					}
					$this->_out(sprintf('/Coords [%.3F %.3F %.3F %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $ir, $grad['coords'][2], $grad['coords'][3], $grad['coords'][4]));
					$this->_out('/Function ' . $f1 . ' 0 R');
					$this->_out('/Extend [' . $grad['extend'][0] . ' ' . $grad['extend'][1] . '] ');
					$this->_out('>>');
				} else if ($grad['type'] == 6) {
					$this->_out('/BitsPerCoordinate 16');
					$this->_out('/BitsPerComponent 8');
					if ($grad['colorspace'] == 'CMYK') {
						$this->_out('/Decode[0 1 0 1 0 1 0 1 0 1 0 1]');
					} else if ($grad['colorspace'] == 'Gray') {
						$this->_out('/Decode[0 1 0 1 0 1]');
					} else {
						$this->_out('/Decode[0 1 0 1 0 1 0 1 0 1]');
					}
					$this->_out('/BitsPerFlag 8');
					$this->_out('/Length ' . strlen($grad['stream']));
					$this->_out('>>');
					$this->_putstream($grad['stream']);
				}
				$this->_out('endobj');
			}

			$this->gradients[$id]['id'] = $this->n;

			// set pattern object
			$this->_newobj();
			$out = '<< /Type /Pattern /PatternType 2';
			$out .= ' /Shading ' . $this->gradients[$id]['id'] . ' 0 R';
			$out .= ' >>';
			$out .= "\n" . 'endobj';
			$this->_out($out);


			$this->gradients[$id]['pattern'] = $this->n;

			if (isset($grad['trans']) && $grad['trans']) {
				// luminosity pattern
				$transid = $id + $maxid;
				$this->_newobj();
				$this->_out('<<');
				$this->_out('/ShadingType ' . $grad['type']);
				$this->_out('/ColorSpace /DeviceGray');
				if ($grad['type'] == 2) {
					$this->_out(sprintf('/Coords [%.3F %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3]));
					$this->_out('/Function ' . $f2 . ' 0 R');
					$this->_out('/Extend [' . $grad['extend'][0] . ' ' . $grad['extend'][1] . '] ');
					$this->_out('>>');
				} else if ($grad['type'] == 3) {
					//x0, y0, r0, x1, y1, r1
					//at this this time radius of inner circle is 0
					$ir = 0;
					if (isset($grad['coords'][5]) && $grad['coords'][5]) {
						$ir = $grad['coords'][5];
					}
					$this->_out(sprintf('/Coords [%.3F %.3F %.3F %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $ir, $grad['coords'][2], $grad['coords'][3], $grad['coords'][4]));
					$this->_out('/Function ' . $f2 . ' 0 R');
					$this->_out('/Extend [' . $grad['extend'][0] . ' ' . $grad['extend'][1] . '] ');
					$this->_out('>>');
				} else if ($grad['type'] == 6) {
					$this->_out('/BitsPerCoordinate 16');
					$this->_out('/BitsPerComponent 8');
					$this->_out('/Decode[0 1 0 1 0 1]');
					$this->_out('/BitsPerFlag 8');
					$this->_out('/Length ' . strlen($grad['stream_trans']));
					$this->_out('>>');
					$this->_putstream($grad['stream_trans']);
				}
				$this->_out('endobj');

				$this->gradients[$transid]['id'] = $this->n;
				$this->_newobj();
				$this->_out('<< /Type /Pattern /PatternType 2');
				$this->_out('/Shading ' . $this->gradients[$transid]['id'] . ' 0 R');
				$this->_out('>>');
				$this->_out('endobj');
				$this->gradients[$transid]['pattern'] = $this->n;
				$this->_newobj();
				// Need to extend size of viewing box in case of transformations
				$str = 'q /a0 gs /Pattern cs /p' . $transid . ' scn -' . ($this->wPt / 2) . ' -' . ($this->hPt / 2) . ' ' . (2 * $this->wPt) . ' ' . (2 * $this->hPt) . ' re f Q';
				$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
				$p = ($this->compress) ? gzcompress($str) : $str;
				$this->_out('<< /Type /XObject /Subtype /Form /FormType 1 ' . $filter);
				$this->_out('/Length ' . strlen($p));
				$this->_out('/BBox [-' . ($this->wPt / 2) . ' -' . ($this->hPt / 2) . ' ' . (2 * $this->wPt) . ' ' . (2 * $this->hPt) . ']');
				$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceGray >>');
				$this->_out('/Resources <<');
				$this->_out('/ExtGState << /a0 << /ca 1 /CA 1 >> >>');
				$this->_out('/Pattern << /p' . $transid . ' ' . $this->gradients[$transid]['pattern'] . ' 0 R >>');
				$this->_out('>>');
				$this->_out('>>');
				$this->_putstream($p);
				$this->_out('endobj');
				$this->_newobj();
				$this->_out('<< /Type /Mask /S /Luminosity /G ' . ($this->n - 1) . ' 0 R >>' . "\n" . 'endobj');
				$this->_newobj();
				$this->_out('<< /Type /ExtGState /SMask ' . ($this->n - 1) . ' 0 R /AIS false >>' . "\n" . 'endobj');
				if (isset($grad['fo']) && $grad['fo']) {
					$this->extgstates[] = array('n' => $this->n, 'trans' => 'TGS' . $id, 'fo' => true);
				} else {
					$this->extgstates[] = array('n' => $this->n, 'trans' => 'TGS' . $id);
				}
			}
		}
	}

	/* -- END BACKGROUNDS -- */

	function _putspotcolors()
	{
		foreach ($this->spotColors as $name => $color) {
			$this->_newobj();
			$this->_out('[/Separation /' . str_replace(' ', '#20', $name));
			$this->_out('/DeviceCMYK <<');
			$this->_out('/Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0] ');
			$this->_out(sprintf('/C1 [%.3F %.3F %.3F %.3F] ', $color['c'] / 100, $color['m'] / 100, $color['y'] / 100, $color['k'] / 100));
			$this->_out('/FunctionType 2 /Domain [0 1] /N 1>>]');
			$this->_out('endobj');
			$this->spotColors[$name]['n'] = $this->n;
		}
	}

	function _putresources()
	{
		if ($this->hasOC || count($this->layers))
			$this->_putocg();
		$this->_putextgstates();
		$this->_putspotcolors();
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '40', 'Compiling Fonts');
		} // *PROGRESS-BAR*
		$this->_putfonts();
		if ($this->progressBar) {
			$this->UpdateProgressBar(2, '50', 'Compiling Images');
		} // *PROGRESS-BAR*
		$this->_putimages();
		$this->_putformobjects(); // *IMAGES-CORE*

		/* -- IMPORTS -- */
		if ($this->enableImports) {
			$this->_putformxobjects();
			$this->_putimportedobjects();
		}
		/* -- END IMPORTS -- */

		/* -- BACKGROUNDS -- */
		$this->_putshaders();
		$this->_putpatterns();
		/* -- END BACKGROUNDS -- */


		//Resource dictionary
		$this->offsets[2] = strlen($this->buffer);
		$this->_out('2 0 obj');
		$this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');

		$this->_out('/Font <<');
		foreach ($this->fonts as $font) {
			if (isset($font['type']) && $font['type'] == 'TTF' && !$font['used']) {
				continue;
			}
			if (isset($font['type']) && $font['type'] == 'TTF' && ($font['sip'] || $font['smp'])) {
				foreach ($font['n'] AS $k => $fid) {
					$this->_out('/F' . $font['subsetfontids'][$k] . ' ' . $font['n'][$k] . ' 0 R');
				}
			} else {
				$this->_out('/F' . $font['i'] . ' ' . $font['n'] . ' 0 R');
			}
		}
		$this->_out('>>');

		if (count($this->spotColors)) {
			$this->_out('/ColorSpace <<');
			foreach ($this->spotColors as $color)
				$this->_out('/CS' . $color['i'] . ' ' . $color['n'] . ' 0 R');
			$this->_out('>>');
		}

		if (count($this->extgstates)) {
			$this->_out('/ExtGState <<');
			foreach ($this->extgstates as $k => $extgstate)
				if (isset($extgstate['trans']))
					$this->_out('/' . $extgstate['trans'] . ' ' . $extgstate['n'] . ' 0 R');
				else
					$this->_out('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
			$this->_out('>>');
		}

		/* -- BACKGROUNDS -- */
		if ((isset($this->gradients) AND ( count($this->gradients) > 0)) || ($this->enableImports && count($this->tpls))) { // mPDF 5.7.3
			$this->_out('/Shading <<');
			foreach ($this->gradients as $id => $grad) {
				$this->_out('/Sh' . $id . ' ' . $grad['id'] . ' 0 R');
			}
			// mPDF 5.7.3
			// If a shading dictionary is in an object (tpl) imported from another PDF, it needs to be included
			// in the document resources, as well as the object resources
			// Otherwise get an error in some PDF viewers
			if ($this->enableImports && count($this->tpls)) {
				foreach ($this->tpls as $tplidx => $tpl) {
					if (isset($tpl['resources'])) {
						$this->current_parser = & $tpl['parser'];
						reset($tpl['resources'][1]);
						while (list($k, $v) = each($tpl['resources'][1])) {
							if ($k == '/Shading') {
								while (list($k2, $v2) = each($v[1])) {
									$this->_out($k2 . " ", false);
									$this->pdf_write_value($v2);
								}
							}
						}
					}
				}
			}

			$this->_out('>>');
			/*
			  // ??? Not needed !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			  $this->_out('/Pattern <<');
			  foreach ($this->gradients as $id => $grad) {
			  $this->_out('/P'.$id.' '.$grad['pattern'].' 0 R');
			  }
			  $this->_out('>>');
			 */
		}
		/* -- END BACKGROUNDS -- */


		if (count($this->images) || count($this->formobjects) || ($this->enableImports && count($this->tpls))) {
			$this->_out('/XObject <<');
			foreach ($this->images as $image)
				$this->_out('/I' . $image['i'] . ' ' . $image['n'] . ' 0 R');
			foreach ($this->formobjects as $formobject)
				$this->_out('/FO' . $formobject['i'] . ' ' . $formobject['n'] . ' 0 R');
			/* -- IMPORTS -- */
			if ($this->enableImports && count($this->tpls)) {
				foreach ($this->tpls as $tplidx => $tpl) {
					$this->_out($this->tplprefix . $tplidx . ' ' . $tpl['n'] . ' 0 R');
				}
			}
			/* -- END IMPORTS -- */
			$this->_out('>>');
		}

		/* -- BACKGROUNDS -- */

		if (count($this->patterns)) {
			$this->_out('/Pattern <<');
			foreach ($this->patterns as $k => $patterns)
				$this->_out('/P' . $k . ' ' . $patterns['n'] . ' 0 R');
			$this->_out('>>');
		}
		/* -- END BACKGROUNDS -- */

		if ($this->hasOC || count($this->layers)) {
			$this->_out('/Properties <<');
			if ($this->hasOC) {
				$this->_out('/OC1 ' . $this->n_ocg_print . ' 0 R /OC2 ' . $this->n_ocg_view . ' 0 R /OC3 ' . $this->n_ocg_hidden . ' 0 R ');
			}
			if (count($this->layers)) {
				foreach ($this->layers as $id => $layer)
					$this->_out('/ZI' . $id . ' ' . $layer['n'] . ' 0 R');
			}
			$this->_out('>>');
		}

		$this->_out('>>');
		$this->_out('endobj'); // end resource dictionary

		$this->_putbookmarks();  // *BOOKMARKS*

		if (isset($this->js) && $this->js) {
			$this->_putjavascript();
		}

		/* -- ENCRYPTION -- */
		if ($this->encrypted) {
			$this->_newobj();
			$this->enc_obj_id = $this->n;
			$this->_out('<<');
			$this->_putencryption();
			$this->_out('>>');
			$this->_out('endobj');
		}
		/* -- END ENCRYPTION -- */
	}

	function _putjavascript()
	{
		$this->_newobj();
		$this->n_js = $this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) ' . (1 + $this->n) . ' 0 R ]');
		$this->_out('>>');
		$this->_out('endobj');

		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS ' . $this->_textstring($this->js));
		$this->_out('>>');
		$this->_out('endobj');
	}

	/* -- ENCRYPTION -- */

	function _putencryption()
	{
		$this->_out('/Filter /Standard');
		if ($this->useRC128encryption) {
			$this->_out('/V 2');
			$this->_out('/R 3');
			$this->_out('/Length 128');
		} else {
			$this->_out('/V 1');
			$this->_out('/R 2');
		}
		$this->_out('/O (' . $this->_escape($this->Ovalue) . ')');
		$this->_out('/U (' . $this->_escape($this->Uvalue) . ')');
		$this->_out('/P ' . $this->Pvalue);
	}

	/* -- END ENCRYPTION -- */

	function _puttrailer()
	{
		$this->_out('/Size ' . ($this->n + 1));
		$this->_out('/Root ' . $this->n . ' 0 R');
		$this->_out('/Info ' . $this->InfoRoot . ' 0 R');
		/* -- ENCRYPTION -- */
		if ($this->encrypted) {
			$this->_out('/Encrypt ' . $this->enc_obj_id . ' 0 R');
			$this->_out('/ID [<' . $this->uniqid . '> <' . $this->uniqid . '>]');
		} else {
			/* -- END ENCRYPTION -- */
			$uniqid = md5(time() . $this->buffer);
			$this->_out('/ID [<' . $uniqid . '> <' . $uniqid . '>]');
			/* -- ENCRYPTION -- */
		}
		/* -- END ENCRYPTION -- */
	}

	/* -- ENCRYPTION -- */


	/* -- END ENCRYPTION -- */

//=========================================
	/* -- BOOKMARKS -- */
// FROM class PDF_Bookmark

	function Bookmark($txt, $level = 0, $y = 0)
	{
		$txt = $this->purify_utf8_text($txt);
		if ($this->text_input_as_HTML) {
			$txt = $this->all_entities_to_utf8($txt);
		}
		if ($y == -1) {
			if (!$this->ColActive) {
				$y = $this->y;
			} else {
				$y = $this->y0;
			} // If columns are on - mark top of columns
		}
		// else y is used as set, or =0 i.e. top of page
		// DIRECTIONALITY RTL
		$bmo = array('t' => $txt, 'l' => $level, 'y' => $y, 'p' => $this->page);
		if ($this->keep_block_together) {
			// do nothing
		}
		/* -- TABLES -- */ else if ($this->table_rotate) {
			$this->tbrot_BMoutlines[] = $bmo;
		} else if ($this->kwt) {
			$this->kwt_BMoutlines[] = $bmo;
		}
		/* -- END TABLES -- */ else if ($this->ColActive) { // *COLUMNS*
			$this->col_BMoutlines[] = $bmo; // *COLUMNS*
		} // *COLUMNS*
		else {
			$this->BMoutlines[] = $bmo;
		}
	}

	function _putbookmarks()
	{
		$nb = count($this->BMoutlines);
		if ($nb == 0)
			return;

		$bmo = $this->BMoutlines;
		$this->BMoutlines = array();
		$lastlevel = -1;
		for ($i = 0; $i < count($bmo); $i++) {
			if ($bmo[$i]['l'] > 0) {
				while ($bmo[$i]['l'] - $lastlevel > 1) { // If jump down more than one level, insert a new entry
					$new = $bmo[$i];
					$new['t'] = "[" . $new['t'] . "]"; // Put [] around text/title to highlight
					$new['l'] = $lastlevel + 1;
					$lastlevel++;
					$this->BMoutlines[] = $new;
				}
			}
			$this->BMoutlines[] = $bmo[$i];
			$lastlevel = $bmo[$i]['l'];
		}
		$nb = count($this->BMoutlines);

		$lru = array();
		$level = 0;
		foreach ($this->BMoutlines as $i => $o) {
			if ($o['l'] > 0) {
				$parent = $lru[$o['l'] - 1];
				//Set parent and last pointers
				$this->BMoutlines[$i]['parent'] = $parent;
				$this->BMoutlines[$parent]['last'] = $i;
				if ($o['l'] > $level) {
					//Level increasing: set first pointer
					$this->BMoutlines[$parent]['first'] = $i;
				}
			} else {
				$this->BMoutlines[$i]['parent'] = $nb;
			}
			if ($o['l'] <= $level and $i > 0) {
				//Set prev and next pointers
				$prev = $lru[$o['l']];
				$this->BMoutlines[$prev]['next'] = $i;
				$this->BMoutlines[$i]['prev'] = $prev;
			}
			$lru[$o['l']] = $i;
			$level = $o['l'];
		}


		//Outline items
		$n = $this->n + 1;
		foreach ($this->BMoutlines as $i => $o) {
			$this->_newobj();
			$this->_out('<</Title ' . $this->_UTF16BEtextstring($o['t']));
			$this->_out('/Parent ' . ($n + $o['parent']) . ' 0 R');
			if (isset($o['prev']))
				$this->_out('/Prev ' . ($n + $o['prev']) . ' 0 R');
			if (isset($o['next']))
				$this->_out('/Next ' . ($n + $o['next']) . ' 0 R');
			if (isset($o['first']))
				$this->_out('/First ' . ($n + $o['first']) . ' 0 R');
			if (isset($o['last']))
				$this->_out('/Last ' . ($n + $o['last']) . ' 0 R');


			if (isset($this->pageDim[$o['p']]['h'])) {
				$h = $this->pageDim[$o['p']]['h'];
			} else {
				$h = 0;
			}

			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.3F null]', 1 + 2 * ($o['p']), ($h - $o['y']) * _MPDFK));
			if (isset($this->bookmarkStyles) && isset($this->bookmarkStyles[$o['l']])) {
				// font style
				$bms = $this->bookmarkStyles[$o['l']]['style'];
				$style = 0;
				if (strpos($bms, 'B') !== false) {
					$style += 2;
				}
				if (strpos($bms, 'I') !== false) {
					$style += 1;
				}
				$this->_out(sprintf('/F %d', $style));
				// Colour
				$col = $this->bookmarkStyles[$o['l']]['color'];
				if (isset($col) && is_array($col) && count($col) == 3) {
					$this->_out(sprintf('/C [%.3F %.3F %.3F]', ($col[0] / 255), ($col[1] / 255), ($col[2] / 255)));
				}
			}

			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root
		$this->_newobj();
		$this->OutlineRoot = $this->n;
		$this->_out('<</Type /BMoutlines /First ' . $n . ' 0 R');
		$this->_out('/Last ' . ($n + $lru[0]) . ' 0 R>>');
		$this->_out('endobj');
	}

	/* -- END BOOKMARKS -- */



	/* -- END TOC -- */





	/* -- END INDEX -- */

	function AcceptPageBreak()
	{
		if (count($this->cellBorderBuffer)) {
			$this->printcellbuffer();
		} // *TABLES*
		/* -- COLUMNS -- */
		if ($this->ColActive == 1) {
			if ($this->CurrCol < $this->NbCol - 1) {
				//Go to the next column
				$this->CurrCol++;
				$this->SetCol($this->CurrCol);
				$this->y = $this->y0;
				$this->ChangeColumn = 1; // Number (and direction) of columns changed +1, +2, -2 etc.
				// DIRECTIONALITY RTL
				if ($this->directionality == 'rtl') {
					$this->ChangeColumn = -($this->ChangeColumn);
				} // *OTL*
				//Stay on the page
				return false;
			} else {
				//Go back to the first column - NEW PAGE
				if (count($this->columnbuffer)) {
					$this->printcolumnbuffer();
				}
				$this->SetCol(0);
				$this->y0 = $this->tMargin;
				$this->ChangeColumn = -($this->NbCol - 1);
				// DIRECTIONALITY RTL
				if ($this->directionality == 'rtl') {
					$this->ChangeColumn = -($this->ChangeColumn);
				} // *OTL*
				//Page break
				return true;
			}
		}
		/* -- END COLUMNS -- */
		/* -- TABLES -- */ else if ($this->table_rotate) {
			if ($this->tablebuffer) {
				$this->printtablebuffer();
			}
			return true;
		}
		/* -- END TABLES -- */ else { // *COLUMNS*
			$this->ChangeColumn = 0;
			return $this->autoPageBreak;
		} // *COLUMNS*
		return $this->autoPageBreak;
	}




	/* -- END COLUMNS -- */


//==================================================================

	function printfloatbuffer()
	{
		if (count($this->floatbuffer)) {
			$this->objectbuffer = $this->floatbuffer;
			$this->printobjectbuffer(false);
			$this->objectbuffer = array();
			$this->floatbuffer = array();
			$this->floatmargins = array();
		}
	}

//==================================================================
//==================================================================
// Added ELLIPSES and CIRCLES



// ====================================================
// ====================================================

	function magic_reverse_dir(&$chunk, $dir, &$chunkOTLdata)
	{
		/* -- OTL -- */
		if ($this->usingCoreFont) {
			return 0;
		}
		if ($chunk == '') {
			return 0;
		}

		if ($this->biDirectional || $dir == 'rtl') {
			// check if string contains RTL text
			// including any added from OTL tables (in PUA)
			$pregRTLchars = $this->pregRTLchars;
			if (isset($this->CurrentFont['rtlPUAstr']) && $this->CurrentFont['rtlPUAstr']) {
				$pregRTLchars .= $this->CurrentFont['rtlPUAstr'];
			}
			if (!preg_match("/[" . $pregRTLchars . "]/u", $chunk) && $dir != 'rtl') {
				return 0;
			}   // Chunk doesn't contain RTL characters

			$unicode = $this->UTF8StringToArray($chunk, false);

			$is_strong = false;
			if (empty($chunkOTLdata)) {
				$this->getBasicOTLdata($chunkOTLdata, $unicode, $is_strong);
			}

			if (isset($this->CurrentFont['useOTL']) && ($this->CurrentFont['useOTL'] & 0x80)) {
				$useGPOS = true;
			} else {
				$useGPOS = false;
			}

			// NB Returned $chunk may be a shorter string (with adjusted $cOTLdata) by removal of LRE, RLE etc embedding codes.
			list($chunk, $rtl_content) = $this->otl->_bidiSort($unicode, $chunk, $dir, $chunkOTLdata, $useGPOS);

			return $rtl_content;
		}
		/* -- END OTL -- */
		return 0;
	}

	/* -- OTL -- */

	function getBasicOTLdata(&$chunkOTLdata, $unicode, &$is_strong)
	{
		if (!class_exists('otl', false)) {
			include(_MPDF_PATH . 'classes/otl.php');
		}
		if (empty($this->otl)) {
			$this->otl = new otl($this);
		}
		$chunkOTLdata['group'] = '';
		$chunkOTLdata['GPOSinfo'] = array();
		$chunkOTLdata['char_data'] = array();
		foreach ($unicode as $char) {
			$ucd_record = UCDN::get_ucd_record($char);
			$chunkOTLdata['char_data'][] = array('bidi_class' => $ucd_record[2], 'uni' => $char);
			if ($ucd_record[2] == 0 || $ucd_record[2] == 3 || $ucd_record[2] == 4) {
				$is_strong = true;
			} // contains strong character
			if ($ucd_record[0] == UCDN::UNICODE_GENERAL_CATEGORY_NON_SPACING_MARK) {
				$chunkOTLdata['group'] .= 'M';
			} else if ($char == 32 || $char == 12288) {
				$chunkOTLdata['group'] .= 'S';
			} else {
				$chunkOTLdata['group'] .= 'C';
			}
		}
	}


	/* -- END OTL -- */

//
// ****************************
// ****************************


	function SetSubstitutions()
	{
		$subsarray = array();
		@include(_MPDF_PATH . 'includes/subs_win-1252.php');
		$this->substitute = array();
		foreach ($subsarray AS $key => $val) {
			$this->substitute[code2utf($key)] = $val;
		}
	}

	function SubstituteChars($html)
	{
		// only substitute characters between tags
		if (count($this->substitute)) {
			$a = preg_split('/(<.*?>)/ms', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
			$html = '';
			foreach ($a as $i => $e) {
				if ($i % 2 == 0) {
					$e = strtr($e, $this->substitute);
				}
				$html .= $e;
			}
		}
		return $html;
	}

	function SubstituteCharsSIP(&$writehtml_a, &$writehtml_i, &$writehtml_e)
	{
		if (preg_match("/^(.*?)([\x{20000}-\x{2FFFF}]+)(.*)/u", $writehtml_e, $m)) {
			if (isset($this->CurrentFont['sipext']) && $this->CurrentFont['sipext']) {
				$font = $this->CurrentFont['sipext'];
				if (!in_array($font, $this->available_unifonts)) {
					return 0;
				}
				$writehtml_a[$writehtml_i] = $writehtml_e = $m[1];
				array_splice($writehtml_a, $writehtml_i + 1, 0, array('span style="font-family: ' . $font . '"', $m[2], '/span', $m[3]));
				$this->subPos = $writehtml_i;
				return 4;
			}
		}
		return 0;
	}



	function setHiEntitySubstitutions()
	{
		$entarr = array(
			'nbsp' => '160', 'iexcl' => '161', 'cent' => '162', 'pound' => '163', 'curren' => '164', 'yen' => '165', 'brvbar' => '166', 'sect' => '167',
			'uml' => '168', 'copy' => '169', 'ordf' => '170', 'laquo' => '171', 'not' => '172', 'shy' => '173', 'reg' => '174', 'macr' => '175',
			'deg' => '176', 'plusmn' => '177', 'sup2' => '178', 'sup3' => '179', 'acute' => '180', 'micro' => '181', 'para' => '182', 'middot' => '183',
			'cedil' => '184', 'sup1' => '185', 'ordm' => '186', 'raquo' => '187', 'frac14' => '188', 'frac12' => '189', 'frac34' => '190',
			'iquest' => '191', 'Agrave' => '192', 'Aacute' => '193', 'Acirc' => '194', 'Atilde' => '195', 'Auml' => '196', 'Aring' => '197',
			'AElig' => '198', 'Ccedil' => '199', 'Egrave' => '200', 'Eacute' => '201', 'Ecirc' => '202', 'Euml' => '203', 'Igrave' => '204',
			'Iacute' => '205', 'Icirc' => '206', 'Iuml' => '207', 'ETH' => '208', 'Ntilde' => '209', 'Ograve' => '210', 'Oacute' => '211',
			'Ocirc' => '212', 'Otilde' => '213', 'Ouml' => '214', 'times' => '215', 'Oslash' => '216', 'Ugrave' => '217', 'Uacute' => '218',
			'Ucirc' => '219', 'Uuml' => '220', 'Yacute' => '221', 'THORN' => '222', 'szlig' => '223', 'agrave' => '224', 'aacute' => '225',
			'acirc' => '226', 'atilde' => '227', 'auml' => '228', 'aring' => '229', 'aelig' => '230', 'ccedil' => '231', 'egrave' => '232',
			'eacute' => '233', 'ecirc' => '234', 'euml' => '235', 'igrave' => '236', 'iacute' => '237', 'icirc' => '238', 'iuml' => '239',
			'eth' => '240', 'ntilde' => '241', 'ograve' => '242', 'oacute' => '243', 'ocirc' => '244', 'otilde' => '245', 'ouml' => '246',
			'divide' => '247', 'oslash' => '248', 'ugrave' => '249', 'uacute' => '250', 'ucirc' => '251', 'uuml' => '252', 'yacute' => '253',
			'thorn' => '254', 'yuml' => '255', 'OElig' => '338', 'oelig' => '339', 'Scaron' => '352', 'scaron' => '353', 'Yuml' => '376',
			'fnof' => '402', 'circ' => '710', 'tilde' => '732', 'Alpha' => '913', 'Beta' => '914', 'Gamma' => '915', 'Delta' => '916',
			'Epsilon' => '917', 'Zeta' => '918', 'Eta' => '919', 'Theta' => '920', 'Iota' => '921', 'Kappa' => '922', 'Lambda' => '923',
			'Mu' => '924', 'Nu' => '925', 'Xi' => '926', 'Omicron' => '927', 'Pi' => '928', 'Rho' => '929', 'Sigma' => '931', 'Tau' => '932',
			'Upsilon' => '933', 'Phi' => '934', 'Chi' => '935', 'Psi' => '936', 'Omega' => '937', 'alpha' => '945', 'beta' => '946', 'gamma' => '947',
			'delta' => '948', 'epsilon' => '949', 'zeta' => '950', 'eta' => '951', 'theta' => '952', 'iota' => '953', 'kappa' => '954',
			'lambda' => '955', 'mu' => '956', 'nu' => '957', 'xi' => '958', 'omicron' => '959', 'pi' => '960', 'rho' => '961', 'sigmaf' => '962',
			'sigma' => '963', 'tau' => '964', 'upsilon' => '965', 'phi' => '966', 'chi' => '967', 'psi' => '968', 'omega' => '969',
			'thetasym' => '977', 'upsih' => '978', 'piv' => '982', 'ensp' => '8194', 'emsp' => '8195', 'thinsp' => '8201', 'zwnj' => '8204',
			'zwj' => '8205', 'lrm' => '8206', 'rlm' => '8207', 'ndash' => '8211', 'mdash' => '8212', 'lsquo' => '8216', 'rsquo' => '8217',
			'sbquo' => '8218', 'ldquo' => '8220', 'rdquo' => '8221', 'bdquo' => '8222', 'dagger' => '8224', 'Dagger' => '8225', 'bull' => '8226',
			'hellip' => '8230', 'permil' => '8240', 'prime' => '8242', 'Prime' => '8243', 'lsaquo' => '8249', 'rsaquo' => '8250', 'oline' => '8254',
			'frasl' => '8260', 'euro' => '8364', 'image' => '8465', 'weierp' => '8472', 'real' => '8476', 'trade' => '8482', 'alefsym' => '8501',
			'larr' => '8592', 'uarr' => '8593', 'rarr' => '8594', 'darr' => '8595', 'harr' => '8596', 'crarr' => '8629', 'lArr' => '8656',
			'uArr' => '8657', 'rArr' => '8658', 'dArr' => '8659', 'hArr' => '8660', 'forall' => '8704', 'part' => '8706', 'exist' => '8707',
			'empty' => '8709', 'nabla' => '8711', 'isin' => '8712', 'notin' => '8713', 'ni' => '8715', 'prod' => '8719', 'sum' => '8721',
			'minus' => '8722', 'lowast' => '8727', 'radic' => '8730', 'prop' => '8733', 'infin' => '8734', 'ang' => '8736', 'and' => '8743',
			'or' => '8744', 'cap' => '8745', 'cup' => '8746', 'int' => '8747', 'there4' => '8756', 'sim' => '8764', 'cong' => '8773',
			'asymp' => '8776', 'ne' => '8800', 'equiv' => '8801', 'le' => '8804', 'ge' => '8805', 'sub' => '8834', 'sup' => '8835', 'nsub' => '8836',
			'sube' => '8838', 'supe' => '8839', 'oplus' => '8853', 'otimes' => '8855', 'perp' => '8869', 'sdot' => '8901', 'lceil' => '8968',
			'rceil' => '8969', 'lfloor' => '8970', 'rfloor' => '8971', 'lang' => '9001', 'rang' => '9002', 'loz' => '9674', 'spades' => '9824',
			'clubs' => '9827', 'hearts' => '9829', 'diams' => '9830',
		);
		foreach ($entarr AS $key => $val) {
			$this->entsearch[] = '&' . $key . ';';
			$this->entsubstitute[] = code2utf($val);
		}
	}

	function SubstituteHiEntities($html)
	{
		// converts html_entities > ASCII 127 to unicode
		// Leaves in particular &lt; to distinguish from tag marker
		if (count($this->entsearch)) {
			$html = str_replace($this->entsearch, $this->entsubstitute, $html);
		}
		return $html;
	}

// Edited v1.2 Pass by reference; option to continue if invalid UTF-8 chars
	function is_utf8(&$string)
	{
		if ($string === mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
			return true;
		} else {
			if ($this->ignore_invalid_utf8) {
				$string = mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32");
				return true;
			} else {
				return false;
			}
		}
	}

	function purify_utf8($html, $lo = true)
	{
		// For HTML
		// Checks string is valid UTF-8 encoded
		// converts html_entities > ASCII 127 to UTF-8
		// Only exception - leaves low ASCII entities e.g. &lt; &amp; etc.
		// Leaves in particular &lt; to distinguish from tag marker
		if (!$this->is_utf8($html)) {
			echo "<p><b>HTML contains invalid UTF-8 character(s)</b></p>";
			while (mb_convert_encoding(mb_convert_encoding($html, "UTF-32", "UTF-8"), "UTF-8", "UTF-32") != $html) {
				$a = iconv('UTF-8', 'UTF-8', $html);
				echo ($a);
				$pos = $start = strlen($a);
				$err = '';
				while (ord(substr($html, $pos, 1)) > 128) {
					$err .= '[[#' . ord(substr($html, $pos, 1)) . ']]';
					$pos++;
				}
				echo '<span style="color:red; font-weight:bold">' . $err . '</span>';
				$html = substr($html, $pos);
			}
			echo $html;
			$this->Error("");
		}
		$html = preg_replace("/\r/", "", $html);

		// converts html_entities > ASCII 127 to UTF-8
		// Leaves in particular &lt; to distinguish from tag marker
		$html = $this->SubstituteHiEntities($html);

		// converts all &#nnn; or &#xHHH; to UTF-8 multibyte
		// If $lo==true then includes ASCII < 128
		$html = strcode2utf($html, $lo);
		return ($html);
	}

	function purify_utf8_text($txt)
	{
		// For TEXT
		// Make sure UTF-8 string of characters
		if (!$this->is_utf8($txt)) {
			$this->Error("Text contains invalid UTF-8 character(s)");
		}

		$txt = preg_replace("/\r/", "", $txt);

		return ($txt);
	}

	function all_entities_to_utf8($txt)
	{
		// converts txt_entities > ASCII 127 to UTF-8
		// Leaves in particular &lt; to distinguish from tag marker
		$txt = $this->SubstituteHiEntities($txt);

		// converts all &#nnn; or &#xHHH; to UTF-8 multibyte
		$txt = strcode2utf($txt);

		$txt = $this->lesser_entity_decode($txt);
		return ($txt);
	}






	/* -- END COLUMNS -- */

	function ConvertColor($color = "#000000")
	{
		static $cache;

		$color = trim(strtolower($color));
		$c = false;
		$cstr = '';
		if ($color == 'transparent') {
			return false;
		} else if ($color == 'inherit') {
			return false;
		} else if (isset($this->SVGcolors[$color]))
			$color = $this->SVGcolors[$color];

		if (!isset($cache[$color])) {
			if (preg_match('/^[\d]+$/', $color)) {
				$c = (array(1, $color));
			} // i.e. integer only
			else if ($color[0] == '#') { //case of #nnnnnn or #nnn
				$cor = preg_replace('/\s+.*/', '', $color); // in case of Background: #CCC url() x-repeat etc.
				if (strlen($cor) == 4) { // Turn #RGB into #RRGGBB
					$cor = "#" . $cor[1] . $cor[1] . $cor[2] . $cor[2] . $cor[3] . $cor[3];
				}
				$r = hexdec(substr($cor, 1, 2));
				$g = hexdec(substr($cor, 3, 2));
				$b = hexdec(substr($cor, 5, 2));
				$c = array(3, $r, $g, $b);
			} else if (preg_match('/(rgba|rgb|device-cmyka|cmyka|device-cmyk|cmyk|hsla|hsl|spot)\((.*?)\)/', $color, $m)) {
				$type = $m[1];
				$cores = explode(",", $m[2]);
				$ncores = count($cores);
				if (stristr($cores[0], '%')) {
					$cores[0] += 0;
					if ($type == 'rgb' || $type == 'rgba') {
						$cores[0] = intval($cores[0] * 255 / 100);
					}
				}
				if ($ncores > 1 && stristr($cores[1], '%')) {
					$cores[1] += 0;
					if ($type == 'rgb' || $type == 'rgba') {
						$cores[1] = intval($cores[1] * 255 / 100);
					}
					if ($type == 'hsl' || $type == 'hsla') {
						$cores[1] = $cores[1] / 100;
					}
				}
				if ($ncores > 2 && stristr($cores[2], '%')) {
					$cores[2] += 0;
					if ($type == 'rgb' || $type == 'rgba') {
						$cores[2] = intval($cores[2] * 255 / 100);
					}
					if ($type == 'hsl' || $type == 'hsla') {
						$cores[2] = $cores[2] / 100;
					}
				}
				if ($ncores > 3 && stristr($cores[3], '%')) {
					$cores[3] += 0;
				}

				if ($type == 'rgb') {
					$c = array(3, $cores[0], $cores[1], $cores[2]);
				} else if ($type == 'rgba') {
					$c = array(5, $cores[0], $cores[1], $cores[2], $cores[3] * 100);
				} else if ($type == 'cmyk' || $type == 'device-cmyk') {
					$c = array(4, $cores[0], $cores[1], $cores[2], $cores[3]);
				} else if ($type == 'cmyka' || $type == 'device-cmyka') {
					$c = array(6, $cores[0], $cores[1], $cores[2], $cores[3], $cores[4] * 100);
				} else if ($type == 'hsl' || $type == 'hsla') {
					$conv = $this->hsl2rgb($cores[0] / 360, $cores[1], $cores[2]);
					if ($type == 'hsl') {
						$c = array(3, $conv[0], $conv[1], $conv[2]);
					} else if ($type == 'hsla') {
						$c = array(5, $conv[0], $conv[1], $conv[2], $cores[3] * 100);
					}
				} else if ($type == 'spot') {
					$name = strtoupper(trim($cores[0]));
					if (!isset($this->spotColors[$name])) {
						if (isset($cores[5])) {
							$this->AddSpotColor($cores[0], $cores[2], $cores[3], $cores[4], $cores[5]);
						} else {
							$this->Error('Undefined spot color: ' . $name);
						}
					}
					$c = array(2, $this->spotColors[$name]['i'], $cores[1]);
				}
			}


			// $this->restrictColorSpace
			// 1 - allow GRAYSCALE only [convert CMYK/RGB->gray]
			// 2 - allow RGB / SPOT COLOR / Grayscale [convert CMYK->RGB]
			// 3 - allow CMYK / SPOT COLOR / Grayscale [convert RGB->CMYK]
			if ($this->PDFA || $this->PDFX || $this->restrictColorSpace) {
				if ($c[0] == 1) { // GRAYSCALE
				} else if ($c[0] == 2) { // SPOT COLOR
					if (!isset($this->spotColorIDs[$c[1]])) {
						$this->Error('Error: Spot colour has not been defined - ' . $this->spotColorIDs[$c[1]]);
					}
					if ($this->PDFA) {
						if ($this->PDFA && !$this->PDFAauto) {
							$this->PDFAXwarnings[] = "Spot color specified '" . $this->spotColorIDs[$c[1]] . "' (converted to process color)";
						}
						if ($this->restrictColorSpace != 3) {
							$sp = $this->spotColors[$this->spotColorIDs[$c[1]]];
							$c = $this->cmyk2rgb(array(4, $sp['c'], $sp['m'], $sp['y'], $sp['k']));
						}
					} else if ($this->restrictColorSpace == 1) {
						$sp = $this->spotColors[$this->spotColorIDs[$c[1]]];
						$c = $this->cmyk2gray(array(4, $sp['c'], $sp['m'], $sp['y'], $sp['k']));
					}
				}
				// RGB
				else if ($c[0] == 3) {
					if ($this->PDFX || ($this->PDFA && $this->restrictColorSpace == 3)) {
						if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) {
							$this->PDFAXwarnings[] = "RGB color specified '" . $color . "' (converted to CMYK)";
						}
						$c = $this->rgb2cmyk($c);
					} else if ($this->restrictColorSpace == 1) {
						$c = $this->rgb2gray($c);
					} else if ($this->restrictColorSpace == 3) {
						$c = $this->rgb2cmyk($c);
					}
				}
				// CMYK
				else if ($c[0] == 4) {
					if ($this->PDFA && $this->restrictColorSpace != 3) {
						if ($this->PDFA && !$this->PDFAauto) {
							$this->PDFAXwarnings[] = "CMYK color specified '" . $color . "' (converted to RGB)";
						}
						$c = $this->cmyk2rgb($c);
					} else if ($this->restrictColorSpace == 1) {
						$c = $this->cmyk2gray($c);
					} else if ($this->restrictColorSpace == 2) {
						$c = $this->cmyk2rgb($c);
					}
				}
				// RGBa
				else if ($c[0] == 5) {
					if ($this->PDFX || ($this->PDFA && $this->restrictColorSpace == 3)) {
						if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) {
							$this->PDFAXwarnings[] = "RGB color with transparency specified '" . $color . "' (converted to CMYK without transparency)";
						}
						$c = $this->rgb2cmyk($c);
						$c = array(4, $c[1], $c[2], $c[3], $c[4]);
					} else if ($this->PDFA && $this->restrictColorSpace != 3) {
						if (!$this->PDFAauto) {
							$this->PDFAXwarnings[] = "RGB color with transparency specified '" . $color . "' (converted to RGB without transparency)";
						}
						$c = $this->rgb2cmyk($c);
						$c = array(4, $c[1], $c[2], $c[3], $c[4]);
					} else if ($this->restrictColorSpace == 1) {
						$c = $this->rgb2gray($c);
					} else if ($this->restrictColorSpace == 3) {
						$c = $this->rgb2cmyk($c);
					}
				}
				// CMYKa
				else if ($c[0] == 6) {
					if ($this->PDFA && $this->restrictColorSpace != 3) {
						if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) {
							$this->PDFAXwarnings[] = "CMYK color with transparency specified '" . $color . "' (converted to RGB without transparency)";
						}
						$c = $this->cmyk2rgb($c);
						$c = array(3, $c[1], $c[2], $c[3]);
					} else if ($this->PDFX || ($this->PDFA && $this->restrictColorSpace == 3)) {
						if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) {
							$this->PDFAXwarnings[] = "CMYK color with transparency specified '" . $color . "' (converted to CMYK without transparency)";
						}
						$c = $this->cmyk2rgb($c);
						$c = array(3, $c[1], $c[2], $c[3]);
					} else if ($this->restrictColorSpace == 1) {
						$c = $this->cmyk2gray($c);
					} else if ($this->restrictColorSpace == 2) {
						$c = $this->cmyk2rgb($c);
					}
				}
			}
			if (is_array($c)) {
				$c = array_pad($c, 6, 0);
				$cstr = pack("a1ccccc", $c[0], ($c[1] & 0xFF), ($c[2] & 0xFF), ($c[3] & 0xFF), ($c[4] & 0xFF), ($c[5] & 0xFF));
			}

			$cache[$color] = $cstr;
		}

		return $cache[$color];
	}


	

	function ConvertSize($size = 5, $maxsize = 0, $fontsize = false, $usefontsize = true)
	{
// usefontsize - set false for e.g. margins - will ignore fontsize for % values
// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
// For text $maxsize = Fontsize
// Setting e.g. margin % will use maxsize (pagewidth) and em will use fontsize
// Returns values using 'mm' units
		$size = trim(strtolower($size));

		if ($size == 'thin')
			$size = 1 * (25.4 / $this->dpi); //1 pixel width for table borders
		elseif (stristr($size, 'px'))
			$size *= (25.4 / $this->dpi); //pixels
		elseif (stristr($size, 'cm'))
			$size *= 10; //centimeters
		elseif (stristr($size, 'mm'))
			$size += 0; //millimeters
		elseif (stristr($size, 'pt'))
			$size *= 25.4 / 72; //72 pts/inch
		elseif (stristr($size, 'rem')) {
			$size += 0; //make "0.83rem" become simply "0.83"
			$size *= ($this->default_font_size / _MPDFK);
		} elseif (stristr($size, 'em')) {
			$size += 0; //make "0.83em" become simply "0.83"
			if ($fontsize) {
				$size *= $fontsize;
			} else {
				$size *= $maxsize;
			}
		} elseif (stristr($size, '%')) {
			$size += 0; //make "90%" become simply "90"
			if ($fontsize && $usefontsize) {
				$size *= $fontsize / 100;
			} else {
				$size *= $maxsize / 100;
			}
		} elseif (stristr($size, 'in'))
			$size *= 25.4; //inches
		elseif (stristr($size, 'pc'))
			$size *= 38.1 / 9; //PostScript picas
		elseif (stristr($size, 'ex')) { // Approximates "ex" as half of font height
			$size += 0; //make "3.5ex" become simply "3.5"
			if ($fontsize) {
				$size *= $fontsize / 2;
			} else {
				$size *= $maxsize / 2;
			}
		} elseif ($size == 'medium')
			$size = 3 * (25.4 / $this->dpi); //3 pixel width for table borders
		elseif ($size == 'thick')
			$size = 5 * (25.4 / $this->dpi); //5 pixel width for table borders
		elseif ($size == 'xx-small') {
			if ($fontsize) {
				$size *= $fontsize * 0.7;
			} else {
				$size *= $maxsize * 0.7;
			}
		} elseif ($size == 'x-small') {
			if ($fontsize) {
				$size *= $fontsize * 0.77;
			} else {
				$size *= $maxsize * 0.77;
			}
		} elseif ($size == 'small') {
			if ($fontsize) {
				$size *= $fontsize * 0.86;
			} else {
				$size *= $maxsize * 0.86;
			}
		} elseif ($size == 'medium') {
			if ($fontsize) {
				$size *= $fontsize;
			} else {
				$size *= $maxsize;
			}
		} elseif ($size == 'large') {
			if ($fontsize) {
				$size *= $fontsize * 1.2;
			} else {
				$size *= $maxsize * 1.2;
			}
		} elseif ($size == 'x-large') {
			if ($fontsize) {
				$size *= $fontsize * 1.5;
			} else {
				$size *= $maxsize * 1.5;
			}
		} elseif ($size == 'xx-large') {
			if ($fontsize) {
				$size *= $fontsize * 2;
			} else {
				$size *= $maxsize * 2;
			}
		} else
			$size *= (25.4 / $this->dpi); //nothing == px

		return $size;
	}

	function lesser_entity_decode($html)
	{
		//supports the most used entity codes (only does ascii safe characters)
		$html = str_replace("&lt;", "<", $html);
		$html = str_replace("&gt;", ">", $html);

		$html = str_replace("&apos;", "'", $html);
		$html = str_replace("&quot;", '"', $html);
		$html = str_replace("&amp;", "&", $html);
		return $html;
	}

	function AdjustHTML($html, $tabSpaces = 8)
	{
		//Try to make the html text more manageable (turning it into XHTML)
		if (PHP_VERSION_ID < 50307) {
			if (strlen($html) > 100000) {
				if (PHP_VERSION_ID < 50200)
					$this->Error("The HTML code is more than 100,000 characters. You should use WriteHTML() with smaller string lengths.");
				else
					ini_set("pcre.backtrack_limit", "1000000");
			}
		}

		/* -- ANNOTATIONS -- */
		preg_match_all("/(<annotation.*?>)/si", $html, $m);
		if (count($m[1])) {
			for ($i = 0; $i < count($m[1]); $i++) {
				$sub = preg_replace("/\n/si", "\xbb\xa4\xac", $m[1][$i]);
				$html = preg_replace('/' . preg_quote($m[1][$i], '/') . '/si', $sub, $html);
			}
		}
		/* -- END ANNOTATIONS -- */

		preg_match_all("/(<svg.*?<\/svg>)/si", $html, $svgi);
		if (count($svgi[0])) {
			for ($i = 0; $i < count($svgi[0]); $i++) {
				$file = _MPDF_TEMP_PATH . '_tempSVG' . uniqid(rand(1, 100000), true) . '_' . $i . '.svg';
				//Save to local file
				file_put_contents($file, $svgi[0][$i]);
				$html = str_replace($svgi[0][$i], '<img src="' . $file . '" />', $html);
			}
		}

		//Remove javascript code from HTML (should not appear in the PDF file)
		$html = preg_replace('/<script.*?<\/script>/is', '', $html);

		//Remove special comments
		$html = preg_replace('/<!--mpdf/i', '', $html);
		$html = preg_replace('/mpdf-->/i', '', $html);

		//Remove comments from HTML (should not appear in the PDF file)
		$html = preg_replace('/<!--.*?-->/s', '', $html);

		$html = preg_replace('/\f/', '', $html); //replace formfeed by nothing
		$html = preg_replace('/\r/', '', $html); //replace carriage return by nothing
		// Well formed XHTML end tags
		$html = preg_replace('/<(br|hr)>/i', "<\\1 />", $html); // mPDF 6
		$html = preg_replace('/<(br|hr)\/>/i', "<\\1 />", $html);
		// Get rid of empty <thead></thead> etc
		$html = preg_replace('/<tr>\s*<\/tr>/i', '', $html);
		$html = preg_replace('/<thead>\s*<\/thead>/i', '', $html);
		$html = preg_replace('/<tfoot>\s*<\/tfoot>/i', '', $html);
		$html = preg_replace('/<table[^>]*>\s*<\/table>/i', '', $html);

		// Remove spaces at end of table cells
		$html = preg_replace("/[ \n\r]+<\/t(d|h)/", '</t\\1', $html);

		$html = preg_replace("/[ ]*<dottab\s*[\/]*>[ ]*/", '<dottab />', $html);

		// Concatenates any Substitute characters from symbols/dingbats
		$html = str_replace('</tts><tts>', '|', $html);
		$html = str_replace('</ttz><ttz>', '|', $html);
		$html = str_replace('</tta><tta>', '|', $html);

		$html = preg_replace('/<br \/>\s*/is', "<br />", $html);

		$html = preg_replace('/<wbr[ \/]*>\s*/is', "&#173;", $html);

		// Preserve '\n's in content between the tags <pre> and </pre>
		if (preg_match('/<pre/', $html)) {
			$html_a = preg_split('/(\<\/?pre[^\>]*\>)/', $html, -1, 2);
			$h = array();
			$c = 0;
			foreach ($html_a AS $s) {
				if ($c > 1 && preg_match('/^<\/pre/i', $s)) {
					$c--;
					$s = preg_replace('/<\/pre/i', '</innerpre', $s);
				} else if ($c > 0 && preg_match('/^<pre/i', $s)) {
					$c++;
					$s = preg_replace('/<pre/i', '<innerpre', $s);
				} else if (preg_match('/^<pre/i', $s)) {
					$c++;
				} else if (preg_match('/^<\/pre/i', $s)) {
					$c--;
				}
				array_push($h, $s);
			}
			$html = implode("", $h);
		}
		$thereispre = preg_match_all('#<pre(.*?)>(.*?)</pre>#si', $html, $temp);
		// Preserve '\n's in content between the tags <textarea> and </textarea>
		$thereistextarea = preg_match_all('#<textarea(.*?)>(.*?)</textarea>#si', $html, $temp2);
		$html = preg_replace('/[\n]/', ' ', $html); //replace linefeed by spaces
		$html = preg_replace('/[\t]/', ' ', $html); //replace tabs by spaces
		// Converts < to &lt; when not a tag
		$html = preg_replace('/<([^!\/a-zA-Z_:])/i', '&lt;\\1', $html); // mPDF 5.7.3
		$html = preg_replace("/[ ]+/", ' ', $html);

		$html = preg_replace('/\/li>\s+<\/(u|o)l/i', '/li></\\1l', $html);
		$html = preg_replace('/\/(u|o)l>\s+<\/li/i', '/\\1l></li', $html);
		$html = preg_replace('/\/li>\s+<\/(u|o)l/i', '/li></\\1l', $html);
		$html = preg_replace('/\/li>\s+<li/i', '/li><li', $html);
		$html = preg_replace('/<(u|o)l([^>]*)>[ ]+/i', '<\\1l\\2>', $html);
		$html = preg_replace('/[ ]+<(u|o)l/i', '<\\1l', $html);

		// Make self closing tabs valid XHTML
		// Tags which are self-closing: 1) Replaceable and 2) Non-replaced items
		$selftabs = 'input|hr|img|br|jpgraph|barcode|dottab';
		$selftabs2 = 'indexentry|indexinsert|bookmark|watermarktext|watermarkimage|column_break|columnbreak|newcolumn|newpage|page_break|pagebreak|formfeed|columns|toc|tocpagebreak|setpageheader|setpagefooter|sethtmlpageheader|sethtmlpagefooter|annotation';
		$html = preg_replace('/(<(' . $selftabs . '|' . $selftabs2 . ')[^>\/]*)>/i', '\\1 />', $html);

		$iterator = 0;
		while ($thereispre) { //Recover <pre attributes>content</pre>
			$temp[2][$iterator] = preg_replace('/<([^!\/a-zA-Z_:])/', '&lt;\\1', $temp[2][$iterator]); // mPDF 5.7.2	// mPDF 5.7.3

			$temp[2][$iterator] = preg_replace_callback("/^([^\n\t]*?)\t/m", array($this, 'tabs2spaces_callback'), $temp[2][$iterator]); // mPDF 5.7+
			$temp[2][$iterator] = preg_replace('/\t/', str_repeat(" ", $tabSpaces), $temp[2][$iterator]);

			$temp[2][$iterator] = preg_replace('/\n/', "<br />", $temp[2][$iterator]);
			$temp[2][$iterator] = str_replace('\\', "\\\\", $temp[2][$iterator]);
			//$html = preg_replace('#<pre(.*?)>(.*?)</pre>#si','<erp'.$temp[1][$iterator].'>'.$temp[2][$iterator].'</erp>',$html,1);
			$html = preg_replace('#<pre(.*?)>(.*?)</pre>#si', '<erp' . $temp[1][$iterator] . '>' . str_replace('$', '\$', $temp[2][$iterator]) . '</erp>', $html, 1); // mPDF 5.7+
			$thereispre--;
			$iterator++;
		}
		$iterator = 0;
		while ($thereistextarea) { //Recover <textarea attributes>content</textarea>
			$temp2[2][$iterator] = preg_replace('/\t/', str_repeat(" ", $tabSpaces), $temp2[2][$iterator]);
			$temp2[2][$iterator] = str_replace('\\', "\\\\", $temp2[2][$iterator]);
			$html = preg_replace('#<textarea(.*?)>(.*?)</textarea>#si', '<aeratxet' . $temp2[1][$iterator] . '>' . trim($temp2[2][$iterator]) . '</aeratxet>', $html, 1);
			$thereistextarea--;
			$iterator++;
		}
		//Restore original tag names
		$html = str_replace("<erp", "<pre", $html);
		$html = str_replace("</erp>", "</pre>", $html);
		$html = str_replace("<aeratxet", "<textarea", $html);
		$html = str_replace("</aeratxet>", "</textarea>", $html);
		$html = str_replace("</innerpre", "</pre", $html);
		$html = str_replace("<innerpre", "<pre", $html);

		$html = preg_replace('/<textarea([^>]*)><\/textarea>/si', '<textarea\\1> </textarea>', $html);
		$html = preg_replace('/(<table[^>]*>)\s*(<caption)(.*?<\/caption>)(.*?<\/table>)/si', '\\2 position="top"\\3\\1\\4\\2 position="bottom"\\3', $html); // *TABLES*
		$html = preg_replace('/<(h[1-6])([^>]*)(>(?:(?!h[1-6]).)*?<\/\\1>\s*<table)/si', '<\\1\\2 keep-with-table="1"\\3', $html); // *TABLES*
		$html = preg_replace("/\xbb\xa4\xac/", "\n", $html);

		// Fixes <p>&#8377</p> which browser copes with even though it is wrong!
		$html = preg_replace("/(&#[x]{0,1}[0-9a-f]{1,5})</i", "\\1;<", $html);
		return $html;
	}








	
}

//end of Class
?>
