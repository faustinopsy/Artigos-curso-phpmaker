<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "conteudoinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$conteudo_edit = NULL; // Initialize page object first

class cconteudo_edit extends cconteudo {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{62B16F30-7B01-4FF2-AFCB-D258EDB35A44}";

	// Table name
	var $TableName = 'conteudo';

	// Page object name
	var $PageObjName = 'conteudo_edit';

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

		// Table object (conteudo)
		if (!isset($GLOBALS["conteudo"]) || get_class($GLOBALS["conteudo"]) == "cconteudo") {
			$GLOBALS["conteudo"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["conteudo"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'conteudo', TRUE);

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
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("conteudolist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		if ($Security->IsLoggedIn()) {
			$Security->UserID_Loading();
			$Security->LoadUserID();
			$Security->UserID_Loaded();
			if (strval($Security->CurrentUserID()) == "") {
				$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
				$this->Page_Terminate(ew_GetUrl("conteudolist.php"));
			}
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->idcontent->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		global $EW_EXPORT, $conteudo;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($conteudo);
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
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["idcontent"] <> "") {
			$this->idcontent->setQueryStringValue($_GET["idcontent"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->idcontent->CurrentValue == "")
			$this->Page_Terminate("conteudolist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("conteudolist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->imagem->Upload->Index = $objForm->Index;
		$this->imagem->Upload->UploadFile();
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->idcontent->FldIsDetailKey)
			$this->idcontent->setFormValue($objForm->GetValue("x_idcontent"));
		if (!$this->titulo->FldIsDetailKey) {
			$this->titulo->setFormValue($objForm->GetValue("x_titulo"));
		}
		if (!$this->conteudo->FldIsDetailKey) {
			$this->conteudo->setFormValue($objForm->GetValue("x_conteudo"));
		}
		if (!$this->aprov->FldIsDetailKey) {
			$this->aprov->setFormValue($objForm->GetValue("x_aprov"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->idcontent->CurrentValue = $this->idcontent->FormValue;
		$this->titulo->CurrentValue = $this->titulo->FormValue;
		$this->conteudo->CurrentValue = $this->conteudo->FormValue;
		$this->aprov->CurrentValue = $this->aprov->FormValue;
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

		// Check if valid user id
		if ($res) {
			$res = $this->ShowOptionLink('edit');
			if (!$res) {
				$sUserIdMsg = $Language->Phrase("NoPermission");
				$this->setFailureMessage($sUserIdMsg);
			}
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
		$this->visual->setDbValue($rs->fields('visual'));
		$this->user->setDbValue($rs->fields('user'));
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
		$this->visual->DbValue = $row['visual'];
		$this->user->DbValue = $row['user'];
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
		// visual
		// user

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// idcontent
		$this->idcontent->ViewValue = $this->idcontent->CurrentValue;
		$this->idcontent->ViewCustomAttributes = "";

		// titulo
		$this->titulo->ViewValue = $this->titulo->CurrentValue;
		$this->titulo->ViewCustomAttributes = "";

		// imagem
		if (!ew_Empty($this->imagem->Upload->DbValue)) {
			$this->imagem->ImageWidth = 300;
			$this->imagem->ImageHeight = 240;
			$this->imagem->ImageAlt = $this->imagem->FldAlt();
			$this->imagem->ViewValue = "conteudo_imagem_bv.php?" . "idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;
			$this->imagem->IsBlobImage = ew_IsImageFile("image" . ew_ContentExt(substr($this->imagem->Upload->DbValue, 0, 11)));
		} else {
			$this->imagem->ViewValue = "";
		}
		$this->imagem->ViewCustomAttributes = "";

		// conteudo
		$this->conteudo->ViewValue = $this->conteudo->CurrentValue;
		$this->conteudo->ViewCustomAttributes = "";

		// aprov
		$this->aprov->ViewValue = $this->aprov->CurrentValue;
		$this->aprov->ViewCustomAttributes = "";

			// idcontent
			$this->idcontent->LinkCustomAttributes = "";
			$this->idcontent->HrefValue = "";
			$this->idcontent->TooltipValue = "";

			// titulo
			$this->titulo->LinkCustomAttributes = "";
			$this->titulo->HrefValue = "";
			$this->titulo->TooltipValue = "";

			// imagem
			$this->imagem->LinkCustomAttributes = "";
			if (!empty($this->imagem->Upload->DbValue)) {
				$this->imagem->HrefValue = "conteudo_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue;
				$this->imagem->LinkAttrs["target"] = "_blank";
				if ($this->Export <> "") $this->imagem->HrefValue = ew_ConvertFullUrl($this->imagem->HrefValue);
			} else {
				$this->imagem->HrefValue = "";
			}
			$this->imagem->HrefValue2 = "conteudo_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;
			$this->imagem->TooltipValue = "";
			if ($this->imagem->UseColorbox) {
				$this->imagem->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
				$this->imagem->LinkAttrs["data-rel"] = "conteudo_x_imagem";

				//$this->imagem->LinkAttrs["class"] = "ewLightbox ewTooltip img-thumbnail";
				//$this->imagem->LinkAttrs["data-placement"] = "bottom";
				//$this->imagem->LinkAttrs["data-container"] = "body";

				$this->imagem->LinkAttrs["class"] = "ewLightbox img-thumbnail";
			}

			// conteudo
			$this->conteudo->LinkCustomAttributes = "";
			$this->conteudo->HrefValue = "";
			$this->conteudo->TooltipValue = "";

			// aprov
			$this->aprov->LinkCustomAttributes = "";
			$this->aprov->HrefValue = "";
			$this->aprov->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// idcontent
			$this->idcontent->EditAttrs["class"] = "form-control";
			$this->idcontent->EditCustomAttributes = "";
			$this->idcontent->EditValue = $this->idcontent->CurrentValue;
			$this->idcontent->ViewCustomAttributes = "";

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
				$this->imagem->EditValue = "conteudo_imagem_bv.php?" . "idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;
				$this->imagem->IsBlobImage = ew_IsImageFile("image" . ew_ContentExt(substr($this->imagem->Upload->DbValue, 0, 11)));
			} else {
				$this->imagem->EditValue = "";
			}
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->imagem);

			// conteudo
			$this->conteudo->EditAttrs["class"] = "form-control";
			$this->conteudo->EditCustomAttributes = "";
			$this->conteudo->EditValue = ew_HtmlEncode($this->conteudo->CurrentValue);
			$this->conteudo->PlaceHolder = ew_RemoveHtml($this->conteudo->FldCaption());

			// aprov
			// Edit refer script
			// idcontent

			$this->idcontent->HrefValue = "";

			// titulo
			$this->titulo->HrefValue = "";

			// imagem
			if (!empty($this->imagem->Upload->DbValue)) {
				$this->imagem->HrefValue = "conteudo_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue;
				$this->imagem->LinkAttrs["target"] = "_blank";
				if ($this->Export <> "") $this->imagem->HrefValue = ew_ConvertFullUrl($this->imagem->HrefValue);
			} else {
				$this->imagem->HrefValue = "";
			}
			$this->imagem->HrefValue2 = "conteudo_imagem_bv.php?idcontent=" . $this->idcontent->CurrentValue . "&amp;showthumbnail=1&amp;thumbnailwidth=" . $this->imagem->ImageWidth . "&amp;thumbnailheight=" . $this->imagem->ImageHeight;

			// conteudo
			$this->conteudo->HrefValue = "";

			// aprov
			$this->aprov->HrefValue = "";
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

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// titulo
			$this->titulo->SetDbValueDef($rsnew, $this->titulo->CurrentValue, "", $this->titulo->ReadOnly);

			// imagem
			if (!($this->imagem->ReadOnly) && !$this->imagem->Upload->KeepFile) {
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
			$this->conteudo->SetDbValueDef($rsnew, $this->conteudo->CurrentValue, "", $this->conteudo->ReadOnly);

			// aprov
			$this->aprov->SetDbValueDef($rsnew, 2, 0);
			$rsnew['aprov'] = &$this->aprov->DbValue;

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
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
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// imagem
		ew_CleanUploadTempPath($this->imagem, $this->imagem->Upload->Index);
		return $EditRow;
	}

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->user->CurrentValue);
		return TRUE;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "conteudolist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($conteudo_edit)) $conteudo_edit = new cconteudo_edit();

// Page init
$conteudo_edit->Page_Init();

// Page main
$conteudo_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$conteudo_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fconteudoedit = new ew_Form("fconteudoedit", "edit");

// Validate form
fconteudoedit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $conteudo->titulo->FldCaption(), $conteudo->titulo->ReqErrMsg)) ?>");
			felm = this.GetElements("x" + infix + "_imagem");
			elm = this.GetElements("fn_x" + infix + "_imagem");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, "<?php echo ew_JsEncode2(str_replace("%s", $conteudo->imagem->FldCaption(), $conteudo->imagem->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_conteudo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $conteudo->conteudo->FldCaption(), $conteudo->conteudo->ReqErrMsg)) ?>");

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
fconteudoedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fconteudoedit.ValidateRequired = true;
<?php } else { ?>
fconteudoedit.ValidateRequired = false; 
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
<?php $conteudo_edit->ShowPageHeader(); ?>
<?php
$conteudo_edit->ShowMessage();
?>
<form name="fconteudoedit" id="fconteudoedit" class="<?php echo $conteudo_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($conteudo_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $conteudo_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="conteudo">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($conteudo->idcontent->Visible) { // idcontent ?>
	<div id="r_idcontent" class="form-group">
		<label id="elh_conteudo_idcontent" class="col-sm-2 control-label ewLabel"><?php echo $conteudo->idcontent->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $conteudo->idcontent->CellAttributes() ?>>
<span id="el_conteudo_idcontent">
<span<?php echo $conteudo->idcontent->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $conteudo->idcontent->EditValue ?></p></span>
</span>
<input type="hidden" data-table="conteudo" data-field="x_idcontent" name="x_idcontent" id="x_idcontent" value="<?php echo ew_HtmlEncode($conteudo->idcontent->CurrentValue) ?>">
<?php echo $conteudo->idcontent->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($conteudo->titulo->Visible) { // titulo ?>
	<div id="r_titulo" class="form-group">
		<label id="elh_conteudo_titulo" for="x_titulo" class="col-sm-2 control-label ewLabel"><?php echo $conteudo->titulo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $conteudo->titulo->CellAttributes() ?>>
<span id="el_conteudo_titulo">
<input type="text" data-table="conteudo" data-field="x_titulo" name="x_titulo" id="x_titulo" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($conteudo->titulo->getPlaceHolder()) ?>" value="<?php echo $conteudo->titulo->EditValue ?>"<?php echo $conteudo->titulo->EditAttributes() ?>>
</span>
<?php echo $conteudo->titulo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($conteudo->imagem->Visible) { // imagem ?>
	<div id="r_imagem" class="form-group">
		<label id="elh_conteudo_imagem" class="col-sm-2 control-label ewLabel"><?php echo $conteudo->imagem->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $conteudo->imagem->CellAttributes() ?>>
<span id="el_conteudo_imagem">
<div id="fd_x_imagem">
<span title="<?php echo $conteudo->imagem->FldTitle() ? $conteudo->imagem->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($conteudo->imagem->ReadOnly || $conteudo->imagem->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="conteudo" data-field="x_imagem" name="x_imagem" id="x_imagem"<?php echo $conteudo->imagem->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x_imagem" id= "fn_x_imagem" value="<?php echo $conteudo->imagem->Upload->FileName ?>">
<?php if (@$_POST["fa_x_imagem"] == "0") { ?>
<input type="hidden" name="fa_x_imagem" id= "fa_x_imagem" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_imagem" id= "fa_x_imagem" value="1">
<?php } ?>
<input type="hidden" name="fs_x_imagem" id= "fs_x_imagem" value="0">
<input type="hidden" name="fx_x_imagem" id= "fx_x_imagem" value="<?php echo $conteudo->imagem->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_imagem" id= "fm_x_imagem" value="<?php echo $conteudo->imagem->UploadMaxFileSize ?>">
</div>
<table id="ft_x_imagem" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $conteudo->imagem->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($conteudo->conteudo->Visible) { // conteudo ?>
	<div id="r_conteudo" class="form-group">
		<label id="elh_conteudo_conteudo" class="col-sm-2 control-label ewLabel"><?php echo $conteudo->conteudo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $conteudo->conteudo->CellAttributes() ?>>
<span id="el_conteudo_conteudo">
<?php ew_AppendClass($conteudo->conteudo->EditAttrs["class"], "editor"); ?>
<textarea data-table="conteudo" data-field="x_conteudo" name="x_conteudo" id="x_conteudo" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($conteudo->conteudo->getPlaceHolder()) ?>"<?php echo $conteudo->conteudo->EditAttributes() ?>><?php echo $conteudo->conteudo->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fconteudoedit", "x_conteudo", 35, 4, <?php echo ($conteudo->conteudo->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $conteudo->conteudo->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $conteudo_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fconteudoedit.Init();
</script>
<?php
$conteudo_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$conteudo_edit->Page_Terminate();
?>
