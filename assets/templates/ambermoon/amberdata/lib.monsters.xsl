<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor" xmlns:str="http://exslt.org/strings" xmlns:php="http://php.net/xsl">

	<xsl:import href="farah://slothsoft@amber/games/ambermoon/convert/globals/dictionary" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/convert/globals/extract" />

	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame" />
		</amberdata>
	</xsl:template>

	<xsl:template match="sse:savegame">
		<xsl:variable name="characters" select="sse:archive[@name='Monster_char_data.amb']/*" />
		<xsl:if test="count($characters)">
			<saa:monster-list>
				<xsl:variable name="categories" select="saa:getDictionary('monster-images')" />
				<xsl:for-each select="$categories">
					<xsl:variable name="category" select="." />
					<saa:monster-category name="{@val}">
						<xsl:for-each select="$characters">
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
		<saa:monster image-id="{.//*[@name='gfx-id']/@value}">
			<xsl:call-template name="extract-character">
				<xsl:with-param name="root" select="$root" />
				<xsl:with-param name="id" select="$id" />
				<xsl:with-param name="dialog" select="/.." />
			</xsl:call-template>

			<saa:race>
				<xsl:apply-templates select=".//*[@name = 'race']" mode="attr">
					<xsl:with-param name="name" select="'name'" />
				</xsl:apply-templates>
				<xsl:for-each select=".//*[@name = 'attributes']/*">
					<saa:attribute name="{saa:getName()}" current="{*[@name = 'current']/@value + *[@name = 'current-mod']/@value}" maximum="{*[@name = 'current']/@value}" />
				</xsl:for-each>

				<xsl:variable name="age" select=".//*[@name = 'age']/*" />
				<saa:age current="{$age[@name='current']/@value}" maximum="{$age[@name='current']/@value}" />
			</saa:race>
			<saa:class-instance>
				<xsl:apply-templates select=".//*[@name = 'name']" mode="attr">
					<xsl:with-param name="name" select="'name'" />
				</xsl:apply-templates>
				<xsl:apply-templates select=".//*[@name = 'spellbooks']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'apr-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'hp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'sp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'tp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'slp-per-level']" mode="attr" />
				<xsl:apply-templates select=".//*[@name = 'combat-experience']" mode="attr">
					<xsl:with-param name="name" select="'experience'" />
				</xsl:apply-templates>
				<xsl:for-each select=".//*[@name = 'skills']/*">
					<saa:skill name="{saa:getName()}" current="{*[@name = 'current']/@value + *[@name = 'current-mod']/@value}" maximum="{*[@name = 'current']/@value}" />
				</xsl:for-each>

				<xsl:variable name="hp" select="*[@name = 'hit-points']/*" />
				<saa:hp current="{$hp[@name='current']/@value}" maximum="{$hp[@name='current']/@value + $hp[@name='maximum-mod']/@value}" />

				<xsl:variable name="sp" select="*[@name = 'spell-points']/*" />
				<saa:sp current="{$sp[@name='current']/@value}" maximum="{$sp[@name='current']/@value + $sp[@name='maximum-mod']/@value}" />

				<xsl:call-template name="extract-spellbook-instance" />
			</saa:class-instance>

			<xsl:for-each select="(.//*[@name = 'gfx'])[1]">
				<xsl:call-template name="extract-gfx">
					<xsl:with-param name="id" select="$id" />
				</xsl:call-template>
			</xsl:for-each>
		</saa:monster>
	</xsl:template>

	<xsl:template name="extract-gfx">
		<xsl:param name="id" />

		<xsl:variable name="width" select=".//*[@name = 'width']" />
		<xsl:variable name="height" select=".//*[@name = 'height']" />
		<xsl:variable name="animationCycles" select=".//*[@name = 'cycle']" />
		<xsl:variable name="animationLengths" select=".//*[@name = 'length']" />
		<xsl:variable name="animationMirrors" select=".//*[@name = 'mirror']/*" />

		<saa:gfx archive="Monster_gfx.amb" file="{.//*[@name='gfx-id']/@value}" palette="3" group="monster-id" id="{$id}" position="0" sourge-width="{$width/*[@name = 'source']/@value}"
			source-height="{$height/*[@name = 'source']/@value}" target-width="{$width/*[@name = 'target']/@value}" target-height="{$height/*[@name = 'target']/@value}">
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