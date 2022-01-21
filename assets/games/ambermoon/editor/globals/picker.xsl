<?xml version="1.0" encoding="UTF-8"?><xsl:stylesheet version="1.0" 	xmlns="http://www.w3.org/1999/xhtml"	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 	xmlns:saa="http://schema.slothsoft.net/amber/amberdata">		<xsl:template name="picker.portraits">		<xsl:for-each select="//saa:portrait-list">			<menu type="context" id="amber-picker-portrait" onshow="savegameEditor.openMenu(arguments[0])">				<menuitem label="Ambermoon Portrait Picker" icon="/getResource.php/slothsoft/favicon" />				<xsl:call-template name="picker.menu-items">					<xsl:with-param name="list" select="." />					<xsl:with-param name="name" select="'amber-portrait'" />				</xsl:call-template>			</menu>		</xsl:for-each>	</xsl:template>		<xsl:template name="picker.items">		<xsl:for-each select="//saa:item-list">			<menu type="context" id="amber-picker-item" onshow="savegameEditor.openMenu(arguments[0])">				<menuitem label="Ambermoon Item Picker" icon="/getResource.php/slothsoft/favicon" />				<menu label="Gegenstand">					<xsl:call-template name="picker.menu-items">						<xsl:with-param name="list" select="." />						<xsl:with-param name="name" select="'amber-item-id'" />					</xsl:call-template>				</menu>				<menu label="Anzahl">					<xsl:for-each select="str:split(str:padding(101), '')">						<xsl:variable name="val">							<xsl:choose>								<xsl:when test="position() = last()">									<xsl:value-of select="255" />								</xsl:when>								<xsl:otherwise>									<xsl:value-of select="position() - 1" />								</xsl:otherwise>							</xsl:choose>						</xsl:variable>						<menuitem type="radio" radiogroup="item-amount" label="{$val}" data-picker-name="amber-item-amount"							data-picker-value="{$val}" onclick="savegameEditor.closeMenu(arguments[0])" />					</xsl:for-each>				</menu>				<menuitem data-picker-name="amber-identified" type="checkbox" label="Ist Identifiziert"					onclick="savegameEditor.closeMenu(arguments[0])" />				<menuitem data-picker-name="amber-broken" type="checkbox" label="Ist Zerbrochen"					onclick="savegameEditor.closeMenu(arguments[0])" />				<menu label="Magische Ladungen">					<xsl:for-each select="str:split(str:padding(101), '')">						<xsl:variable name="val">							<xsl:choose>								<xsl:when test="position() = last()">									<xsl:value-of select="255" />								</xsl:when>								<xsl:otherwise>									<xsl:value-of select="position() - 1" />								</xsl:otherwise>							</xsl:choose>						</xsl:variable>						<menuitem type="radio" radiogroup="amber-item-charge" label="{$val}" data-picker-name="amber-item-charge"							data-picker-value="{$val}" onclick="savegameEditor.closeMenu(arguments[0])" />					</xsl:for-each>				</menu>			</menu>		</xsl:for-each>	</xsl:template>		<xsl:template name="picker.tileset-icons">		<xsl:for-each select="//saa:tileset-icon-list">			<menu type="context" id="amber-picker-tileset-icon" onshow="savegameEditor.openMenu(arguments[0])">				<menuitem label="Ambermoon Tile Picker" icon="/getResource.php/slothsoft/favicon" />			</menu>		</xsl:for-each>	</xsl:template>	<xsl:template name="picker.menu-items">		<xsl:param name="list" />		<xsl:param name="name" />		<xsl:for-each select="$list/*">			<menu label="{@name}">				<xsl:for-each select="*">					<!--icon="/getResource.php/amber/items/{@id}" -->					<menuitem label="{@name}" type="radio" radiogroup="{$name}" data-picker-name="{$name}"						data-picker-value="{@id}" onclick="savegameEditor.closeMenu(arguments[0])">						<xsl:if test="@slot">							<xsl:attribute name="data-picker-filter-amber-item-id"><xsl:value-of select="@slot" /></xsl:attribute>						</xsl:if>					</menuitem>				</xsl:for-each>			</menu>		</xsl:for-each>	</xsl:template></xsl:stylesheet>