<?php

// titulo
// imagem
// visual

?>
<?php if ($artigos->Visible) { ?>
<!-- <h4 class="ewMasterCaption"><?php echo $artigos->TableCaption() ?></h4> -->
<table id="tbl_artigosmaster" class="table table-bordered table-striped ewViewTable">
<?php echo $artigos->TableCustomInnerHtml ?>
	<tbody>
<?php if ($artigos->titulo->Visible) { // titulo ?>
		<tr id="r_titulo">
			<td><?php echo $artigos->titulo->FldCaption() ?></td>
			<td<?php echo $artigos->titulo->CellAttributes() ?>>
<span id="el_artigos_titulo">
<span<?php echo $artigos->titulo->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($artigos->titulo->ListViewValue())) && $artigos->titulo->LinkAttributes() <> "") { ?>
<a<?php echo $artigos->titulo->LinkAttributes() ?>><?php echo $artigos->titulo->ListViewValue() ?></a>
<?php } else { ?>
<?php echo $artigos->titulo->ListViewValue() ?>
<?php } ?>
</span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($artigos->imagem->Visible) { // imagem ?>
		<tr id="r_imagem">
			<td><?php echo $artigos->imagem->FldCaption() ?></td>
			<td<?php echo $artigos->imagem->CellAttributes() ?>>
<span id="el_artigos_imagem">
<span>
<?php echo ew_GetFileViewTag($artigos->imagem, $artigos->imagem->ListViewValue()) ?>
</span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($artigos->visual->Visible) { // visual ?>
		<tr id="r_visual">
			<td><?php echo $artigos->visual->FldCaption() ?></td>
			<td<?php echo $artigos->visual->CellAttributes() ?>>
<span id="el_artigos_visual">
<span<?php echo $artigos->visual->ViewAttributes() ?>>
<?php echo $artigos->visual->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
