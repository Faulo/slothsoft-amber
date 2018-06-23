<?xml version="1.0" encoding="UTF-8"?><xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"	xmlns:amber="http://schema.slothsoft.net/amber/amberdata" xmlns:sfm="http://schema.slothsoft.net/farah/module"	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exsl="http://exslt.org/common"	xmlns:func="http://exslt.org/functions" xmlns:str="http://exslt.org/strings"	xmlns:save="http://schema.slothsoft.net/savegame/editor" extension-element-prefixes="exsl func str">	<xsl:import href="farah://slothsoft@amber/xsl/editor.data-list" />	<xsl:import href="farah://slothsoft@amber/xsl/editor.savegame" />	<xsl:import href="farah://slothsoft@farah/xsl/module" />	<!-- <xsl:import href="/getTemplate.php/amber/editor.savegame"/> <xsl:key name="item" match="item" use="@id"/> <xsl:variable 		name="items" select="/data/*[@name = 'items']/fragment"/> <xsl:variable name="portraits" select="/data/*[@name = 'portraits']/fragment"/> -->	<xsl:template match="/sfm:fragment">		<div class="Amber Editor"><!--ng-app="Amber" -->			<xsl:apply-templates select="sfm:error" mode="sfm:html" />			<script type="application/javascript"><![CDATA[var savegameEditor;window.addEventListener(	"DOMContentLoaded",	function(eve) {		savegameEditor = new SavegameEditor(document.querySelector(".Amber"));	},	false);			]]></script>			<xsl:apply-templates select="*[@name='editor']" />			<!-- <div class="pickers"> <xsl:apply-templates select=".//amber:item-list" mode="picker" /> <xsl:apply-templates select=".//amber:portrait-list" 				mode="picker" /> <menu type="context" id="amber-picker-tile" onshow="savegameEditor.openMenu(arguments[0])"> <menuitem label="Ambermoon 				Tile Picker" icon="/getResource.php/slothsoft/favicon" /> </menu> </div> -->			<div class="popup" onclick="savegameEditor.closePopup(arguments[0])" />		</div>	</xsl:template>	<xsl:template match="*[@name='editor']">		<xsl:for-each select="save:savegame.editor">			<xsl:variable name="selectedArchives" select="save:archive[*]" />			<details open="open">				<summary>					<h2>Einstellungen</h2>				</summary>				<div>					<form method="POST" action="./?SaveName={@save-id}" enctype="multipart/form-data">						<div>							<h3 class="name">Aktuelle Spielstand-ID</h3>							<input type="text" name="save[editor][save-id]" value="{@save-id}" readonly="readonly" size="40" />						</div>						<div>							<button class="yellow" type="submit" name="DownloadAll">XML								herunterladen</button>						</div>						<div>							<h3 class="name">Bearbeitbare Dateien</h3>							<table>								<thead>									<tr class="gray">										<td>Dateiname</td>										<td>Letzte Änderung</td>										<td>Eigene Version Hochladen</td>									</tr>								</thead>								<tbody>									<xsl:for-each select="save:archive">										<tr>											<td>												<label class="name">													<input type="radio" name="save[editor][archives][]" value="{@name}">														<xsl:if test="@name = $selectedArchives/@name">															<xsl:attribute name="checked">checked</xsl:attribute>														</xsl:if>													</input>													<xsl:text> </xsl:text>													<xsl:value-of select="@name" />												</label>											</td>											<td>												<xsl:value-of select="@timestamp" />											</td>											<td>												<input type="file" name="save[{@name}]" />											</td>										</tr>									</xsl:for-each>								</tbody>								<tfoot>									<tr>										<td colspan="2">											<button class="yellow" type="submit">Ausgewählte Datei öffnen</button>										</td>										<td>											<button class="yellow" type="submit">Dateien hochladen</button>										</td>									</tr>								</tfoot>							</table>						</div>					</form>				</div>			</details>			<xsl:for-each select="$selectedArchives">				<details open="open">					<summary>						<h2>							Datei bearbeiten:							<span class="green">								<xsl:value-of select="@name" />							</span>						</h2>					</summary>					<xsl:apply-templates select="." mode="form" />				</details>			</xsl:for-each>		</xsl:for-each>	</xsl:template>	<xsl:template match="amber:portrait-list" mode="picker">		<menu type="context" id="amber-picker-portrait" onshow="savegameEditor.openMenu(arguments[0])">			<menuitem label="Ambermoon Portrait Picker" icon="/getResource.php/slothsoft/favicon" />			<xsl:call-template name="menu.items">				<xsl:with-param name="list" select="." />				<xsl:with-param name="name" select="'amber-portrait'" />			</xsl:call-template>		</menu>	</xsl:template>	<xsl:template match="amber:item-list" mode="picker">		<menu type="context" id="amber-picker-item" onshow="savegameEditor.openMenu(arguments[0])">			<menuitem label="Ambermoon Item Picker" icon="/getResource.php/slothsoft/favicon" />			<menu label="Gegenstand">				<xsl:call-template name="menu.items">					<xsl:with-param name="list" select="." />					<xsl:with-param name="name" select="'amber-item-id'" />				</xsl:call-template>			</menu>			<menu label="Anzahl">				<xsl:for-each select="str:split(str:padding(101), '')">					<xsl:variable name="val">						<xsl:choose>							<xsl:when test="position() = last()">								<xsl:value-of select="255" />							</xsl:when>							<xsl:otherwise>								<xsl:value-of select="position() - 1" />							</xsl:otherwise>						</xsl:choose>					</xsl:variable>					<menuitem type="radio" radiogroup="item-amount" label="{$val}" data-picker-name="amber-item-amount"						data-picker-value="{$val}" onclick="savegameEditor.closeMenu(arguments[0])" />				</xsl:for-each>			</menu>			<menuitem data-picker-name="amber-identified" type="checkbox" label="Ist Identifiziert"				onclick="savegameEditor.closeMenu(arguments[0])" />			<menuitem data-picker-name="amber-broken" type="checkbox" label="Ist Zerbrochen"				onclick="savegameEditor.closeMenu(arguments[0])" />			<menu label="Magische Ladungen">				<xsl:for-each select="str:split(str:padding(101), '')">					<xsl:variable name="val">						<xsl:choose>							<xsl:when test="position() = last()">								<xsl:value-of select="255" />							</xsl:when>							<xsl:otherwise>								<xsl:value-of select="position() - 1" />							</xsl:otherwise>						</xsl:choose>					</xsl:variable>					<menuitem type="radio" radiogroup="amber-item-charge" label="{$val}" data-picker-name="amber-item-charge"						data-picker-value="{$val}" onclick="savegameEditor.closeMenu(arguments[0])" />				</xsl:for-each>			</menu>		</menu>	</xsl:template>	<xsl:template match="amber:tileset-icon-list" mode="picker">		<menu type="context" id="amber-picker-tileset-icon" onshow="savegameEditor.openMenu(arguments[0])">			<menuitem label="Ambermoon Tile Picker" icon="/getResource.php/slothsoft/favicon" />		</menu>	</xsl:template>	<xsl:template name="menu.items">		<xsl:param name="list" />		<xsl:param name="name" />		<xsl:for-each select="$list/*">			<menu label="{@name}">				<xsl:for-each select="*">					<!--icon="/getResource.php/amber/items/{@id}" -->					<menuitem label="{@name}" type="radio" radiogroup="{$name}" data-picker-name="{$name}"						data-picker-value="{@id}" onclick="savegameEditor.closeMenu(arguments[0])">						<xsl:if test="@slot">							<xsl:attribute name="data-picker-filter-amber-item-id"><xsl:value-of select="@slot" /></xsl:attribute>						</xsl:if>					</menuitem>				</xsl:for-each>			</menu>		</xsl:for-each>	</xsl:template></xsl:stylesheet>