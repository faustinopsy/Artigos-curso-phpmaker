<?php include_once "usuariosinfo.php" ?>
<?php

// Create page object
if (!isset($comentarios_grid)) $comentarios_grid = new ccomentarios_grid();

// Page init
$comentarios_grid->Page_Init();

// Page main
$comentarios_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$comentarios_grid->Page_Render();
?>
<?php if ($comentarios->Export == "") { ?>
<script type="text/javascript">

// Form object
var fcomentariosgrid = new ew_Form("fcomentariosgrid", "grid");
fcomentariosgrid.FormKeyCountName = '<?php echo $comentarios_grid->FormKeyCountName ?>';

// Validate form
fcomentariosgrid.Validate = function() {
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
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;
			elm = this.GetElements("x" + infix + "_coment");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $comentarios->coment->FldCaption(), $comentarios->coment->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	return true;
}

// Check empty row
fcomentariosgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "coment", false)) return false;
	return true;
}

// Form_CustomValidate event
fcomentariosgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcomentariosgrid.ValidateRequired = true;
<?php } else { ?>
fcomentariosgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcomentariosgrid.Lists["x_user"] = {"LinkField":"x_iduser","Ajax":true,"AutoFill":false,"DisplayFields":["x__email","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"usuarios"};

// Form object for search
</script>
<?php } ?>
<?php
if ($comentarios->CurrentAction == "gridadd") {
	if ($comentarios->CurrentMode == "copy") {
		$bSelectLimit = $comentarios_grid->UseSelectLimit;
		if ($bSelectLimit) {
			$comentarios_grid->TotalRecs = $comentarios->SelectRecordCount();
			$comentarios_grid->Recordset = $comentarios_grid->LoadRecordset($comentarios_grid->StartRec-1, $comentarios_grid->DisplayRecs);
		} else {
			if ($comentarios_grid->Recordset = $comentarios_grid->LoadRecordset())
				$comentarios_grid->TotalRecs = $comentarios_grid->Recordset->RecordCount();
		}
		$comentarios_grid->StartRec = 1;
		$comentarios_grid->DisplayRecs = $comentarios_grid->TotalRecs;
	} else {
		$comentarios->CurrentFilter = "0=1";
		$comentarios_grid->StartRec = 1;
		$comentarios_grid->DisplayRecs = $comentarios->GridAddRowCount;
	}
	$comentarios_grid->TotalRecs = $comentarios_grid->DisplayRecs;
	$comentarios_grid->StopRec = $comentarios_grid->DisplayRecs;
} else {
	$bSelectLimit = $comentarios_grid->UseSelectLimit;
	if ($bSelectLimit) {
		if ($comentarios_grid->TotalRecs <= 0)
			$comentarios_grid->TotalRecs = $comentarios->SelectRecordCount();
	} else {
		if (!$comentarios_grid->Recordset && ($comentarios_grid->Recordset = $comentarios_grid->LoadRecordset()))
			$comentarios_grid->TotalRecs = $comentarios_grid->Recordset->RecordCount();
	}
	$comentarios_grid->StartRec = 1;
	$comentarios_grid->DisplayRecs = $comentarios_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$comentarios_grid->Recordset = $comentarios_grid->LoadRecordset($comentarios_grid->StartRec-1, $comentarios_grid->DisplayRecs);

	// Set no record found message
	if ($comentarios->CurrentAction == "" && $comentarios_grid->TotalRecs == 0) {
		if (!$Security->CanList())
			$comentarios_grid->setWarningMessage(ew_DeniedMsg());
		if ($comentarios_grid->SearchWhere == "0=101")
			$comentarios_grid->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$comentarios_grid->setWarningMessage($Language->Phrase("NoRecord"));
	}
}
$comentarios_grid->RenderOtherOptions();
?>
<?php $comentarios_grid->ShowPageHeader(); ?>
<?php
$comentarios_grid->ShowMessage();
?>
<?php if ($comentarios_grid->TotalRecs > 0 || $comentarios->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid comentarios">
<div id="fcomentariosgrid" class="ewForm form-inline">
<?php if ($comentarios_grid->ShowOtherOptions) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php
	foreach ($comentarios_grid->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<div id="gmp_comentarios" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table id="tbl_comentariosgrid" class="table ewTable">
<?php echo $comentarios->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$comentarios_grid->RowType = EW_ROWTYPE_HEADER;

// Render list options
$comentarios_grid->RenderListOptions();

// Render list options (header, left)
$comentarios_grid->ListOptions->Render("header", "left");
?>
<?php if ($comentarios->user->Visible) { // user ?>
	<?php if ($comentarios->SortUrl($comentarios->user) == "") { ?>
		<th data-name="user"><div id="elh_comentarios_user" class="comentarios_user"><div class="ewTableHeaderCaption"><?php echo $comentarios->user->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="user"><div><div id="elh_comentarios_user" class="comentarios_user">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $comentarios->user->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($comentarios->user->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($comentarios->user->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($comentarios->coment->Visible) { // coment ?>
	<?php if ($comentarios->SortUrl($comentarios->coment) == "") { ?>
		<th data-name="coment"><div id="elh_comentarios_coment" class="comentarios_coment"><div class="ewTableHeaderCaption"><?php echo $comentarios->coment->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="coment"><div><div id="elh_comentarios_coment" class="comentarios_coment">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $comentarios->coment->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($comentarios->coment->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($comentarios->coment->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$comentarios_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$comentarios_grid->StartRec = 1;
$comentarios_grid->StopRec = $comentarios_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($comentarios_grid->FormKeyCountName) && ($comentarios->CurrentAction == "gridadd" || $comentarios->CurrentAction == "gridedit" || $comentarios->CurrentAction == "F")) {
		$comentarios_grid->KeyCount = $objForm->GetValue($comentarios_grid->FormKeyCountName);
		$comentarios_grid->StopRec = $comentarios_grid->StartRec + $comentarios_grid->KeyCount - 1;
	}
}
$comentarios_grid->RecCnt = $comentarios_grid->StartRec - 1;
if ($comentarios_grid->Recordset && !$comentarios_grid->Recordset->EOF) {
	$comentarios_grid->Recordset->MoveFirst();
	$bSelectLimit = $comentarios_grid->UseSelectLimit;
	if (!$bSelectLimit && $comentarios_grid->StartRec > 1)
		$comentarios_grid->Recordset->Move($comentarios_grid->StartRec - 1);
} elseif (!$comentarios->AllowAddDeleteRow && $comentarios_grid->StopRec == 0) {
	$comentarios_grid->StopRec = $comentarios->GridAddRowCount;
}

// Initialize aggregate
$comentarios->RowType = EW_ROWTYPE_AGGREGATEINIT;
$comentarios->ResetAttrs();
$comentarios_grid->RenderRow();
if ($comentarios->CurrentAction == "gridadd")
	$comentarios_grid->RowIndex = 0;
if ($comentarios->CurrentAction == "gridedit")
	$comentarios_grid->RowIndex = 0;
while ($comentarios_grid->RecCnt < $comentarios_grid->StopRec) {
	$comentarios_grid->RecCnt++;
	if (intval($comentarios_grid->RecCnt) >= intval($comentarios_grid->StartRec)) {
		$comentarios_grid->RowCnt++;
		if ($comentarios->CurrentAction == "gridadd" || $comentarios->CurrentAction == "gridedit" || $comentarios->CurrentAction == "F") {
			$comentarios_grid->RowIndex++;
			$objForm->Index = $comentarios_grid->RowIndex;
			if ($objForm->HasValue($comentarios_grid->FormActionName))
				$comentarios_grid->RowAction = strval($objForm->GetValue($comentarios_grid->FormActionName));
			elseif ($comentarios->CurrentAction == "gridadd")
				$comentarios_grid->RowAction = "insert";
			else
				$comentarios_grid->RowAction = "";
		}

		// Set up key count
		$comentarios_grid->KeyCount = $comentarios_grid->RowIndex;

		// Init row class and style
		$comentarios->ResetAttrs();
		$comentarios->CssClass = "";
		if ($comentarios->CurrentAction == "gridadd") {
			if ($comentarios->CurrentMode == "copy") {
				$comentarios_grid->LoadRowValues($comentarios_grid->Recordset); // Load row values
				$comentarios_grid->SetRecordKey($comentarios_grid->RowOldKey, $comentarios_grid->Recordset); // Set old record key
			} else {
				$comentarios_grid->LoadDefaultValues(); // Load default values
				$comentarios_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$comentarios_grid->LoadRowValues($comentarios_grid->Recordset); // Load row values
		}
		$comentarios->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($comentarios->CurrentAction == "gridadd") // Grid add
			$comentarios->RowType = EW_ROWTYPE_ADD; // Render add
		if ($comentarios->CurrentAction == "gridadd" && $comentarios->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$comentarios_grid->RestoreCurrentRowFormValues($comentarios_grid->RowIndex); // Restore form values
		if ($comentarios->CurrentAction == "gridedit") { // Grid edit
			if ($comentarios->EventCancelled) {
				$comentarios_grid->RestoreCurrentRowFormValues($comentarios_grid->RowIndex); // Restore form values
			}
			if ($comentarios_grid->RowAction == "insert")
				$comentarios->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$comentarios->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($comentarios->CurrentAction == "gridedit" && ($comentarios->RowType == EW_ROWTYPE_EDIT || $comentarios->RowType == EW_ROWTYPE_ADD) && $comentarios->EventCancelled) // Update failed
			$comentarios_grid->RestoreCurrentRowFormValues($comentarios_grid->RowIndex); // Restore form values
		if ($comentarios->RowType == EW_ROWTYPE_EDIT) // Edit row
			$comentarios_grid->EditRowCnt++;
		if ($comentarios->CurrentAction == "F") // Confirm row
			$comentarios_grid->RestoreCurrentRowFormValues($comentarios_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$comentarios->RowAttrs = array_merge($comentarios->RowAttrs, array('data-rowindex'=>$comentarios_grid->RowCnt, 'id'=>'r' . $comentarios_grid->RowCnt . '_comentarios', 'data-rowtype'=>$comentarios->RowType));

		// Render row
		$comentarios_grid->RenderRow();

		// Render list options
		$comentarios_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($comentarios_grid->RowAction <> "delete" && $comentarios_grid->RowAction <> "insertdelete" && !($comentarios_grid->RowAction == "insert" && $comentarios->CurrentAction == "F" && $comentarios_grid->EmptyRow())) {
?>
	<tr<?php echo $comentarios->RowAttributes() ?>>
<?php

// Render list options (body, left)
$comentarios_grid->ListOptions->Render("body", "left", $comentarios_grid->RowCnt);
?>
	<?php if ($comentarios->user->Visible) { // user ?>
		<td data-name="user"<?php echo $comentarios->user->CellAttributes() ?>>
<?php if ($comentarios->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="comentarios" data-field="x_user" name="o<?php echo $comentarios_grid->RowIndex ?>_user" id="o<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->OldValue) ?>">
<?php } ?>
<?php if ($comentarios->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($comentarios->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $comentarios_grid->RowCnt ?>_comentarios_user" class="comentarios_user">
<span<?php echo $comentarios->user->ViewAttributes() ?>>
<?php echo $comentarios->user->ListViewValue() ?></span>
</span>
<?php if ($comentarios->CurrentAction <> "F") { ?>
<input type="hidden" data-table="comentarios" data-field="x_user" name="x<?php echo $comentarios_grid->RowIndex ?>_user" id="x<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->FormValue) ?>">
<input type="hidden" data-table="comentarios" data-field="x_user" name="o<?php echo $comentarios_grid->RowIndex ?>_user" id="o<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="comentarios" data-field="x_user" name="fcomentariosgrid$x<?php echo $comentarios_grid->RowIndex ?>_user" id="fcomentariosgrid$x<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->FormValue) ?>">
<input type="hidden" data-table="comentarios" data-field="x_user" name="fcomentariosgrid$o<?php echo $comentarios_grid->RowIndex ?>_user" id="fcomentariosgrid$o<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->OldValue) ?>">
<?php } ?>
<?php } ?>
<a id="<?php echo $comentarios_grid->PageObjName . "_row_" . $comentarios_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($comentarios->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="comentarios" data-field="x_idcoment" name="x<?php echo $comentarios_grid->RowIndex ?>_idcoment" id="x<?php echo $comentarios_grid->RowIndex ?>_idcoment" value="<?php echo ew_HtmlEncode($comentarios->idcoment->CurrentValue) ?>">
<input type="hidden" data-table="comentarios" data-field="x_idcoment" name="o<?php echo $comentarios_grid->RowIndex ?>_idcoment" id="o<?php echo $comentarios_grid->RowIndex ?>_idcoment" value="<?php echo ew_HtmlEncode($comentarios->idcoment->OldValue) ?>">
<?php } ?>
<?php if ($comentarios->RowType == EW_ROWTYPE_EDIT || $comentarios->CurrentMode == "edit") { ?>
<input type="hidden" data-table="comentarios" data-field="x_idcoment" name="x<?php echo $comentarios_grid->RowIndex ?>_idcoment" id="x<?php echo $comentarios_grid->RowIndex ?>_idcoment" value="<?php echo ew_HtmlEncode($comentarios->idcoment->CurrentValue) ?>">
<?php } ?>
	<?php if ($comentarios->coment->Visible) { // coment ?>
		<td data-name="coment"<?php echo $comentarios->coment->CellAttributes() ?>>
<?php if ($comentarios->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $comentarios_grid->RowCnt ?>_comentarios_coment" class="form-group comentarios_coment">
<textarea data-table="comentarios" data-field="x_coment" name="x<?php echo $comentarios_grid->RowIndex ?>_coment" id="x<?php echo $comentarios_grid->RowIndex ?>_coment" cols="20" rows="3" placeholder="<?php echo ew_HtmlEncode($comentarios->coment->getPlaceHolder()) ?>"<?php echo $comentarios->coment->EditAttributes() ?>><?php echo $comentarios->coment->EditValue ?></textarea>
</span>
<input type="hidden" data-table="comentarios" data-field="x_coment" name="o<?php echo $comentarios_grid->RowIndex ?>_coment" id="o<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->OldValue) ?>">
<?php } ?>
<?php if ($comentarios->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $comentarios_grid->RowCnt ?>_comentarios_coment" class="form-group comentarios_coment">
<textarea data-table="comentarios" data-field="x_coment" name="x<?php echo $comentarios_grid->RowIndex ?>_coment" id="x<?php echo $comentarios_grid->RowIndex ?>_coment" cols="20" rows="3" placeholder="<?php echo ew_HtmlEncode($comentarios->coment->getPlaceHolder()) ?>"<?php echo $comentarios->coment->EditAttributes() ?>><?php echo $comentarios->coment->EditValue ?></textarea>
</span>
<?php } ?>
<?php if ($comentarios->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $comentarios_grid->RowCnt ?>_comentarios_coment" class="comentarios_coment">
<span<?php echo $comentarios->coment->ViewAttributes() ?>>
<?php echo $comentarios->coment->ListViewValue() ?></span>
</span>
<?php if ($comentarios->CurrentAction <> "F") { ?>
<input type="hidden" data-table="comentarios" data-field="x_coment" name="x<?php echo $comentarios_grid->RowIndex ?>_coment" id="x<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->FormValue) ?>">
<input type="hidden" data-table="comentarios" data-field="x_coment" name="o<?php echo $comentarios_grid->RowIndex ?>_coment" id="o<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="comentarios" data-field="x_coment" name="fcomentariosgrid$x<?php echo $comentarios_grid->RowIndex ?>_coment" id="fcomentariosgrid$x<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->FormValue) ?>">
<input type="hidden" data-table="comentarios" data-field="x_coment" name="fcomentariosgrid$o<?php echo $comentarios_grid->RowIndex ?>_coment" id="fcomentariosgrid$o<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$comentarios_grid->ListOptions->Render("body", "right", $comentarios_grid->RowCnt);
?>
	</tr>
<?php if ($comentarios->RowType == EW_ROWTYPE_ADD || $comentarios->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fcomentariosgrid.UpdateOpts(<?php echo $comentarios_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($comentarios->CurrentAction <> "gridadd" || $comentarios->CurrentMode == "copy")
		if (!$comentarios_grid->Recordset->EOF) $comentarios_grid->Recordset->MoveNext();
}
?>
<?php
	if ($comentarios->CurrentMode == "add" || $comentarios->CurrentMode == "copy" || $comentarios->CurrentMode == "edit") {
		$comentarios_grid->RowIndex = '$rowindex$';
		$comentarios_grid->LoadDefaultValues();

		// Set row properties
		$comentarios->ResetAttrs();
		$comentarios->RowAttrs = array_merge($comentarios->RowAttrs, array('data-rowindex'=>$comentarios_grid->RowIndex, 'id'=>'r0_comentarios', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($comentarios->RowAttrs["class"], "ewTemplate");
		$comentarios->RowType = EW_ROWTYPE_ADD;

		// Render row
		$comentarios_grid->RenderRow();

		// Render list options
		$comentarios_grid->RenderListOptions();
		$comentarios_grid->StartRowCnt = 0;
?>
	<tr<?php echo $comentarios->RowAttributes() ?>>
<?php

// Render list options (body, left)
$comentarios_grid->ListOptions->Render("body", "left", $comentarios_grid->RowIndex);
?>
	<?php if ($comentarios->user->Visible) { // user ?>
		<td data-name="user">
<?php if ($comentarios->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_comentarios_user" class="form-group comentarios_user">
<span<?php echo $comentarios->user->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $comentarios->user->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="comentarios" data-field="x_user" name="x<?php echo $comentarios_grid->RowIndex ?>_user" id="x<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="comentarios" data-field="x_user" name="o<?php echo $comentarios_grid->RowIndex ?>_user" id="o<?php echo $comentarios_grid->RowIndex ?>_user" value="<?php echo ew_HtmlEncode($comentarios->user->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($comentarios->coment->Visible) { // coment ?>
		<td data-name="coment">
<?php if ($comentarios->CurrentAction <> "F") { ?>
<span id="el$rowindex$_comentarios_coment" class="form-group comentarios_coment">
<textarea data-table="comentarios" data-field="x_coment" name="x<?php echo $comentarios_grid->RowIndex ?>_coment" id="x<?php echo $comentarios_grid->RowIndex ?>_coment" cols="20" rows="3" placeholder="<?php echo ew_HtmlEncode($comentarios->coment->getPlaceHolder()) ?>"<?php echo $comentarios->coment->EditAttributes() ?>><?php echo $comentarios->coment->EditValue ?></textarea>
</span>
<?php } else { ?>
<span id="el$rowindex$_comentarios_coment" class="form-group comentarios_coment">
<span<?php echo $comentarios->coment->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $comentarios->coment->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="comentarios" data-field="x_coment" name="x<?php echo $comentarios_grid->RowIndex ?>_coment" id="x<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="comentarios" data-field="x_coment" name="o<?php echo $comentarios_grid->RowIndex ?>_coment" id="o<?php echo $comentarios_grid->RowIndex ?>_coment" value="<?php echo ew_HtmlEncode($comentarios->coment->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$comentarios_grid->ListOptions->Render("body", "right", $comentarios_grid->RowCnt);
?>
<script type="text/javascript">
fcomentariosgrid.UpdateOpts(<?php echo $comentarios_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($comentarios->CurrentMode == "add" || $comentarios->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $comentarios_grid->FormKeyCountName ?>" id="<?php echo $comentarios_grid->FormKeyCountName ?>" value="<?php echo $comentarios_grid->KeyCount ?>">
<?php echo $comentarios_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($comentarios->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $comentarios_grid->FormKeyCountName ?>" id="<?php echo $comentarios_grid->FormKeyCountName ?>" value="<?php echo $comentarios_grid->KeyCount ?>">
<?php echo $comentarios_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($comentarios->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fcomentariosgrid">
</div>
<?php

// Close recordset
if ($comentarios_grid->Recordset)
	$comentarios_grid->Recordset->Close();
?>
</div>
</div>
<?php } ?>
<?php if ($comentarios_grid->TotalRecs == 0 && $comentarios->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($comentarios_grid->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($comentarios->Export == "") { ?>
<script type="text/javascript">
fcomentariosgrid.Init();
</script>
<?php } ?>
<?php
$comentarios_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$comentarios_grid->Page_Terminate();
?>
