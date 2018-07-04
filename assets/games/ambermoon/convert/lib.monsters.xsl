<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:str="http://exslt.org/strings"
	extension-element-prefixes="str">

	<xsl:import href="farah://slothsoft@amber/games/ambermoon/convert/global.stable" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/convert/global.extract" />

	<xsl:template match="sse:savegame.editor">
		<xsl:variable name="monsters" select="sse:archive[@name='Monster_char_data.amb']/*" />
		<xsl:if test="count($monsters)">
			<xsl:variable name="categories" select="saa:getDictionary('monster-images')" />
			<saa:monster-list>
				<xsl:for-each select="$categories">
					<xsl:variable name="category" select="." />
					<saa:monster-category name="{@val}">
						<xsl:for-each select="$monsters">
							<xsl:if test=".//*[@name = 'gfx-id']/@value = $category/@key">
								<xsl:call-template name="extract-monster">
									<xsl:with-param name="id" select="position()" />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</saa:monster-category>
				</xsl:for-each>
			</saa:monster-list>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="extract-monster">
		<xsl:param name="root" select="." />
		<xsl:param name="id" />
		<saa:monster id="{$id}" image-id="{.//*[@name='gfx-id']/@value}" attack="{*[@name = 'attack']/@value + *[@name = 'combat-attack']/@value}"
			defense="{*[@name = 'defense']/@value + *[@name = 'combat-defense']/@value}">
			<xsl:apply-templates select=".//*[@name = 'name']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'level']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'attacks-per-round']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'gold']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'food']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'combat-experience']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'magic-attack']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'magic-defense']" mode="attr" />
			<xsl:for-each select=".//*[@name = 'monster-type']/*[@value]">
				<xsl:attribute name="is-{saa:getName()}" />
			</xsl:for-each>
			<saa:race>
				<xsl:apply-templates select=".//*[@name = 'race']" mode="attr">
					<xsl:with-param name="name" select="'name'" />
				</xsl:apply-templates>
				<xsl:apply-templates select=".//*[@name = 'age']//*[@name = 'current']" mode="attr">
					<xsl:with-param name="name" select="'current-age'" />
				</xsl:apply-templates>
				<xsl:apply-templates select=".//*[@name = 'age']//*[@name = 'maximum']" mode="attr">
					<xsl:with-param name="name" select="'maximum-age'" />
				</xsl:apply-templates>
				<xsl:for-each select=".//*[@name = 'attributes']/*">
					<saa:attribute name="{saa:getName()}"
						current="{*[@name = 'current']/@value + *[@name = 'current-mod']/@value}" maximum="{*[@name = 'current']/@value}" />
				</xsl:for-each>
			</saa:race>
			<saa:class>
				<xsl:apply-templates select=".//*[@name = 'name']" mode="attr">
					<xsl:with-param name="name" select="'name'" />
				</xsl:apply-templates>
				<xsl:apply-templates select=".//*[@name = 'school']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'apr-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'hp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'sp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'tp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'slp-per-level']" mode="attr" />
				<xsl:for-each select=".//*[@name = 'skills']/*">
					<saa:skill name="{saa:getName()}" current="{*[@name = 'current']/@value + *[@name = 'current-mod']/@value}"
						maximum="{*[@name = 'current']/@value}" />
				</xsl:for-each>
			</saa:class>
			<xsl:call-template name="extract-equipment" />
			<xsl:call-template name="extract-inventory" />
			<xsl:call-template name="extract-spellbook" />
			<xsl:for-each select="(.//*[@name = 'gfx'])[1]">
				<xsl:call-template name="extract-gfx" >
					<xsl:with-param name="id" select="$id" />
				</xsl:call-template>
			</xsl:for-each>
		</saa:monster>
	</xsl:template>
	
	<xsl:template name="extract-gfx">
		<xsl:param name="id"/>

		<xsl:variable name="width" select=".//*[@name = 'width']" />
		<xsl:variable name="height" select=".//*[@name = 'height']" />
		<xsl:variable name="animationCycles" select=".//*[@name = 'cycle']" />
		<xsl:variable name="animationLengths" select=".//*[@name = 'length']" />
		<xsl:variable name="animationMirrors" select=".//*[@name = 'mirror']/*" />

		<saa:gfx archive="Monster_gfx.amb" file="{.//*[@name='gfx-id']/@value}"  palette="3"
			group="monster-id" 
			id="{$id}" position="0"
			sourge-width="{$width/*[@name = 'source']/@value}"
			source-height="{$height/*[@name = 'source']/@value}" 
			target-width="{$width/*[@name = 'target']/@value}"
			target-height="{$height/*[@name = 'target']/@value}">
			<xsl:for-each select="$animationCycles">
				<xsl:variable name="pos" select="position()" />
				<xsl:variable name="length" select="$animationLengths[$pos]/@value" />
				<saa:animation name="{../@name}">
					<xsl:for-each select="str:tokenize(@value)[position() &lt;= $length]">
						<frame offset="{php:functionString('hexdec', .)}" />
					</xsl:for-each>
					<xsl:if test="$animationMirrors[$pos]/@value">
						<xsl:for-each select="str:tokenize(@value)[position() &lt;= $length]">
							<xsl:sort select="position()" order="descending" />
							<saa:frame offset="{php:functionString('hexdec', .)}" />
						</xsl:for-each>
					</xsl:if>
				</saa:animation>
			</xsl:for-each>
		</saa:gfx>
	</xsl:template>
</xsl:stylesheet>