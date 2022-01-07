<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(2, "mmi_conteudo", $Language->MenuPhrase("2", "MenuText"), "conteudolist.php", -1, "", AllowListMenu('{62B16F30-7B01-4FF2-AFCB-D258EDB35A44}conteudo'), FALSE, FALSE);
$RootMenu->AddMenuItem(4, "mmi_edicao", $Language->MenuPhrase("4", "MenuText"), "edicaolist.php", -1, "", AllowListMenu('{62B16F30-7B01-4FF2-AFCB-D258EDB35A44}edicao'), FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mmi_artigos", $Language->MenuPhrase("5", "MenuText"), "artigoslist.php", -1, "", AllowListMenu('{62B16F30-7B01-4FF2-AFCB-D258EDB35A44}artigos'), FALSE, FALSE);
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
