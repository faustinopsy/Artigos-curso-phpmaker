<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "edicaoinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$edicao_add = NULL; // Initialize page object first

class cedicao_add extends cedicao {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{62B16F30-7B01-4FF2-AFCB-D258EDB35A44}";

	// Table name
	var $TableName = 'edicao';

	// Page object name
	var $PageObjName = 'edicao_add';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (edicao)
		if (!isset($GLOBALS["edicao"]) || get_class($GLOBALS["edicao"]) == "cedicao") {
			$GLOBALS["edicao"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["edicao"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'edicao', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// User table object (usuarios)
		if (!isset($UserTable)) {
			$UserTable = new cusuarios();
			$UserTableConn = Conn($UserTable->DBID);
		}
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("edicaolist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		if ($Security->IsLoggedIn()) {
			$Security->UserID_Loading();
			$Security->LoadUserID();
			$Security->UserID_Loaded();
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $edicao;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($edicao);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewAddForm";
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["idcontent"] != "") {
				$this->idcontent->setQueryStringValue($_GET["idcontent"]);
				$this->setKey("idcontent", $this->idcontent->CurrentValue); // Set up key
			} else {
				$this->setKey("idcontent", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("edicaolist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "edicaoview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD; // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->imagem->Upload->Index = $objForm->Index;
		$this->imagem->Upload->UploadFile();
	}

	// Load default values
	function LoadDefaultValues() {
		$this->titulo->CurrentValue = NULL;
		$this->titulo->OldValue = $this->titulo->CurrentValue;
		$this->imagem->Upload->DbValue = NULL;
		$this->imagem->OldValue = $this->imagem->Upload->DbValue;
		$this->conteudo->CurrentValue = NULL;
		$this->conteudo->OldValue = $this->conteudo->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->titulo->FldIsDetailKey) {
			$this->titulo->setFormValue($objForm->GetValue("x_titulo"));
		}
		if (!$this->conteudo->FldIsDetailKey) {
			$this->conteudo->setFormValue($objForm->GetValue("x_conteudo"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->titulo->CurrentValue = $this->titulo->FormValue;
		$this->conteudo->CurrentValue = $this->conteudo->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->idcontent->setDbValue($rs->fields('idcontent'));
		$this->titulo->setDbValue($rs->fields('titulo'));
		$this->imagem->Upload->DbValue = $rs->fields('imagem');
		if (is_array($this->imagem->Upload->DbValue) || is_object($this->imagem->Upload->DbValue)) // Byte array
			$this->imagem->Upload->DbValue = ew_BytesToStr($this->imagem->Upload->DbValue);
		$this->conteudo->setDbValue($rs->fields('conteudo'));
		$this->aprov->setDbValue($rs->fields('aprov'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->idcontent->DbValue = $row['idcontent'];
		$this->titulo->DbValue = $row['titulo'];
		$this->imagem->Upload->DbValue = $row['imagem'];
		$this->conteudo->DbValue = $row['conteudo'];
		$this->aprov->DbValue = $row['aprov'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("idcontent")) <> "")
			$this->idcontent->CurrentValue = $this->getKey("idcontent"); // idcontent
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// idcontent
		// titulo
		// imagem
		// conteudo
		// aprov

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// titulo
		$this->titulo->ViewValue = $this->titulo->CurrentValue;
		$this->titulo->ViewCustomAttributes = "";

		// imagem
		if (!ew_Empty($this->imagem->Upload->DbValue)) {
			$this->imagem->ImageWidth = 300;
			$this->imagem->ImageHeight = 240;
			$this->imagem->ImageAlt = $this->imagem->FldAlt();
			$this->imagem->ViewValue = "edicao_imagem_bv.php?" . "idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;
			$this->imagem->IsBlobImage = ew_IsImageFile("image" . ew_ContentExt(substr($this->imagem->Upload->DbValue, 0, 11)));
		} else {
			$this->imagem->ViewValue = "";
		}
		$this->imagem->ViewCustomAttributes = "";

		// conteudo
		$this->conteudo->ViewValue = $this->conteudo->CurrentValue;
		$this->conteudo->ViewCustomAttributes = "";

			// titulo
			$this->titulo->LinkCustomAttributes = "";
			$this->titulo->HrefValue = "";
			$this->titulo->TooltipValue = "";

			// imagem
			$this->imagem->LinkCustomAttributes = "";
			if (!empty($this->imagem->Upload->DbValue)) {
				$this->imagem->HrefValue = "edicao_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue;
				$this->imagem->LinkAttrs["target"] = "_blank";
				if ($this->Export <> "") $this->imagem->HrefValue = ew_ConvertFullUrl($this->imagem->HrefValue);
			} else {
				$this->imagem->HrefValue = "";
			}
			$this->imagem->HrefValue2 = "edicao_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;
			$this->imagem->TooltipValue = "";
			if ($this->imagem->UseColorbox) {
				$this->imagem->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
				$this->imagem->LinkAttrs["data-rel"] = "edicao_x_imagem";

				//$this->imagem->LinkAttrs["class"] = "ewLightbox ewTooltip img-thumbnail";
				//$this->imagem->LinkAttrs["data-placement"] = "bottom";
				//$this->imagem->LinkAttrs["data-container"] = "body";

				$this->imagem->LinkAttrs["class"] = "ewLightbox img-thumbnail";
			}

			// conteudo
			$this->conteudo->LinkCustomAttributes = "";
			$this->conteudo->HrefValue = "";
			$this->conteudo->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// titulo
			$this->titulo->EditAttrs["class"] = "form-control";
			$this->titulo->EditCustomAttributes = "";
			$this->titulo->EditValue = ew_HtmlEncode($this->titulo->CurrentValue);
			$this->titulo->PlaceHolder = ew_RemoveHtml($this->titulo->FldCaption());

			// imagem
			$this->imagem->EditAttrs["class"] = "form-control";
			$this->imagem->EditCustomAttributes = "";
			if (!ew_Empty($this->imagem->Upload->DbValue)) {
				$this->imagem->ImageWidth = 300;
				$this->imagem->ImageHeight = 240;
				$this->imagem->ImageAlt = $this->imagem->FldAlt();
				$this->imagem->EditValue = "edicao_imagem_bv.php?" . "idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;
				$this->imagem->IsBlobImage = ew_IsImageFile("image" . ew_ContentExt(substr($this->imagem->Upload->DbValue, 0, 11)));
			} else {
				$this->imagem->EditValue = "";
			}
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->imagem);

			// conteudo
			$this->conteudo->EditAttrs["class"] = "form-control";
			$this->conteudo->EditCustomAttributes = "";
			$this->conteudo->EditValue = ew_HtmlEncode($this->conteudo->CurrentValue);
			$this->conteudo->PlaceHolder = ew_RemoveHtml($this->conteudo->FldCaption());

			// Edit refer script
			// titulo

			$this->titulo->HrefValue = "";

			// imagem
			if (!empty($this->imagem->Upload->DbValue)) {
				$this->imagem->HrefValue = "edicao_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue;
				$this->imagem->LinkAttrs["target"] = "_blank";
				if ($this->Export <> "") $this->imagem->HrefValue = ew_ConvertFullUrl($this->imagem->HrefValue);
			} else {
				$this->imagem->HrefValue = "";
			}
			$this->imagem->HrefValue2 = "edicao_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;

			// conteudo
			$this->conteudo->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->titulo->FldIsDetailKey && !is_null($this->titulo->FormValue) && $this->titulo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->titulo->FldCaption(), $this->titulo->ReqErrMsg));
		}
		if ($this->imagem->Upload->FileName == "" && !$this->imagem->Upload->KeepFile) {
			ew_AddMessage($gsFormError, str_replace("%s", $this->imagem->FldCaption(), $this->imagem->ReqErrMsg));
		}
		if (!$this->conteudo->FldIsDetailKey && !is_null($this->conteudo->FormValue) && $this->conteudo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->conteudo->FldCaption(), $this->conteudo->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// titulo
		$this->titulo->SetDbValueDef($rsnew, $this->titulo->CurrentValue, "", FALSE);

		// imagem
		if (!$this->imagem->Upload->KeepFile) {
			if (is_null($this->imagem->Upload->Value)) {
				$rsnew['imagem'] = NULL;
			} else {
				$this->imagem->Upload->Resize(300, 240);
				$this->imagem->ImageWidth = 300; // Resize width
				$this->imagem->ImageHeight = 240; // Resize height
				$rsnew['imagem'] = $this->imagem->Upload->Value;
			}
		}

		// conteudo
		$this->conteudo->SetDbValueDef($rsnew, $this->conteudo->CurrentValue, "", FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {

				// Get insert id if necessary
				$this->idcontent->setDbValue($conn->Insert_ID());
				$rsnew['idcontent'] = $this->idcontent->DbValue;
				if (!$this->imagem->Upload->KeepFile) {
				}
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// imagem
		ew_CleanUploadTempPath($this->imagem, $this->imagem->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "edicaolist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($edicao_add)) $edicao_add = new cedicao_add();

// Page init
$edicao_add->Page_Init();

// Page main
$edicao_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$edicao_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fedicaoadd = new ew_Form("fedicaoadd", "add");

// Validate form
fedicaoadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_titulo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $edicao->titulo->FldCaption(), $edicao->titulo->ReqErrMsg)) ?>");
			felm = this.GetElements("x" + infix + "_imagem");
			elm = this.GetElements("fn_x" + infix + "_imagem");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, "<?php echo ew_JsEncode2(str_replace("%s", $edicao->imagem->FldCaption(), $edicao->imagem->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_conteudo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $edicao->conteudo->FldCaption(), $edicao->conteudo->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fedicaoadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fedicaoadd.ValidateRequired = true;
<?php } else { ?>
fedicaoadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $edicao_add->ShowPageHeader(); ?>
<?php
$edicao_add->ShowMessage();
?>
<form name="fedicaoadd" id="fedicaoadd" class="<?php echo $edicao_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($edicao_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $edicao_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="edicao">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($edicao->titulo->Visible) { // titulo ?>
	<div id="r_titulo" class="form-group">
		<label id="elh_edicao_titulo" for="x_titulo" class="col-sm-2 control-label ewLabel"><?php echo $edicao->titulo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $edicao->titulo->CellAttributes() ?>>
<span id="el_edicao_titulo">
<input type="text" data-table="edicao" data-field="x_titulo" name="x_titulo" id="x_titulo" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($edicao->titulo->getPlaceHolder()) ?>" value="<?php echo $edicao->titulo->EditValue ?>"<?php echo $edicao->titulo->EditAttributes() ?>>
</span>
<?php echo $edicao->titulo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($edicao->imagem->Visible) { // imagem ?>
	<div id="r_imagem" class="form-group">
		<label id="elh_edicao_imagem" class="col-sm-2 control-label ewLabel"><?php echo $edicao->imagem->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $edicao->imagem->CellAttributes() ?>>
<span id="el_edicao_imagem">
<div id="fd_x_imagem">
<span title="<?php echo $edicao->imagem->FldTitle() ? $edicao->imagem->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($edicao->imagem->ReadOnly || $edicao->imagem->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="edicao" data-field="x_imagem" name="x_imagem" id="x_imagem"<?php echo $edicao->imagem->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x_imagem" id= "fn_x_imagem" value="<?php echo $edicao->imagem->Upload->FileName ?>">
<input type="hidden" name="fa_x_imagem" id= "fa_x_imagem" value="0">
<input type="hidden" name="fs_x_imagem" id= "fs_x_imagem" value="0">
<input type="hidden" name="fx_x_imagem" id= "fx_x_imagem" value="<?php echo $edicao->imagem->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_imagem" id= "fm_x_imagem" value="<?php echo $edicao->imagem->UploadMaxFileSize ?>">
</div>
<table id="ft_x_imagem" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $edicao->imagem->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($edicao->conteudo->Visible) { // conteudo ?>
	<div id="r_conteudo" class="form-group">
		<label id="elh_edicao_conteudo" class="col-sm-2 control-label ewLabel"><?php echo $edicao->conteudo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $edicao->conteudo->CellAttributes() ?>>
<span id="el_edicao_conteudo">
<?php ew_AppendClass($edicao->conteudo->EditAttrs["class"], "editor"); ?>
<textarea data-table="edicao" data-field="x_conteudo" name="x_conteudo" id="x_conteudo" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($edicao->conteudo->getPlaceHolder()) ?>"<?php echo $edicao->conteudo->EditAttributes() ?>><?php echo $edicao->conteudo->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fedicaoadd", "x_conteudo", 35, 4, <?php echo ($edicao->conteudo->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $edicao->conteudo->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $edicao_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fedicaoadd.Init();
</script>
<?php
$edicao_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$edicao_add->Page_Terminate();
?>
