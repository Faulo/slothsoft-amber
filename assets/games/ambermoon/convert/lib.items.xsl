<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:str="http://exslt.org/strings"
	xmlns:set="http://exslt.org/sets" xmlns:math="http://exslt.org/math" xmlns:php="http://php.net/xsl"
	xmlns:save="http://schema.slothsoft.net/savegame/editor" xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:html="http://www.w3.org/1999/xhtml" extension-element-prefixes="exsl func str set math php">

	<xsl:import href="farah://slothsoft@amber/games/ambermoon/convert/global.dictionary" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/convert/global.extract" />
	
	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame.editor" />
		</amberdata>
	</xsl:template>
	
	<xsl:template match="sse:savegame.editor">
		<xsl:variable name="items"
			select="(sse:archive[@name='AM2_CPU'] | sse:archive[@name='AM2_BLIT'])//*[@name = 'items']/*/*" />
		<xsl:variable name="texts" select="sse:archive[@name='Object_texts.amb']" />

		<xsl:if test="count($items)">
			<xsl:variable name="categories" select="set:distinct($items//*[@name = 'type']/@value)" />
			<item-list>
				<xsl:for-each select="$categories">
					<xsl:variable name="category" select="." />
					<item-category name="{saa:getDictionaryOption('item-types', $category)/@val}">
						<xsl:for-each select="$items">
							<xsl:if test=".//*[@name = 'type']/@value = $category">
								<xsl:call-template name="extract-item">
									<xsl:with-param name="id" select="position()" />
									<xsl:with-param name="texts" select="$texts" />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</item-category>
				</xsl:for-each>
			</item-list>
		</xsl:if>
	</xsl:template>

	<xsl:template name="extract-item">
		<xsl:param name="root" select="." />
		<xsl:param name="texts" select="/.." />
		<xsl:param name="id" />

		<item id="{$id}">
			<xsl:variable name="gfxId" select=".//*[@name = 'image-id']/@value"/>
			<xsl:variable name="type" select=".//*[@name = 'type']/@value" />
			<xsl:variable name="subtype" select=".//*[@name = 'subtype']/@value" />
			<xsl:variable name="subsubtype" select=".//*[@name = 'subsubtype']/@value" />
			<xsl:variable name="spell-id" select=".//*[@name = 'spell-id']/@value" />
			<xsl:variable name="spell-type" select=".//*[@name = 'spell-type']/@value" />
			<xsl:variable name="spell-dictionary">
				<xsl:choose>
					<xsl:when test="$spell-type = 0">
						<xsl:value-of select="'spells-white'" />
					</xsl:when>
					<xsl:when test="$spell-type = 1">
						<xsl:value-of select="'spells-blue'" />
					</xsl:when>
					<xsl:when test="$spell-type = 2">
						<xsl:value-of select="'spells-green'" />
					</xsl:when>
					<xsl:when test="$spell-type = 3">
						<xsl:value-of select="'spells-black'" />
					</xsl:when>
					<xsl:when test="$spell-type = 6">
						<xsl:value-of select="'spells-misc'" />
					</xsl:when>
				</xsl:choose>
			</xsl:variable>

			<xsl:apply-templates select=".//sse:integer[@name != ''] | .//sse:string" mode="attr" />

			<xsl:apply-templates select=".//*[@name = 'type']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'hands']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'fingers']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'slot']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'ammunition-type']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'ranged-type']" mode="attr" />

			<xsl:if test=".//*[@name = 'attribute-value']/@value &gt; 0">
				<xsl:apply-templates select=".//*[@name = 'attribute-type']" mode="attr" />
			</xsl:if>
			<xsl:if test=".//*[@name = 'skill-value']/@value &gt; 0">
				<xsl:apply-templates select=".//*[@name = 'skill-type']" mode="attr" />
			</xsl:if>

			<xsl:if test="$spell-id &gt; 0">
				<xsl:attribute name="spell-type">
					<xsl:value-of select="key('string-dictionary', 'spell-types')[position() = $spell-type + 1]/@value" />
				</xsl:attribute>
				<xsl:attribute name="spell-name">
					<xsl:value-of select="key('string-dictionary', $spell-dictionary)[position() = $spell-id]/@value" />
				</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="gender">
				<xsl:choose>
					<xsl:when test=".//*[@name = 'male']/@value &gt; .//*[@name = 'female']/@value">männlich</xsl:when>
					<xsl:when test=".//*[@name = 'male']/@value &lt; .//*[@name = 'female']/@value">weiblich</xsl:when>
					<xsl:otherwise>beide</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:for-each select=".//*[@name = 'properties']/*[@value != '']">
				<xsl:attribute name="is-{saa:getName()}" />
			</xsl:for-each>
			<xsl:for-each select=".//*[@name = 'classes']/*[@value != '']">
				<class name="{saa:getName()}" />
			</xsl:for-each>
			<xsl:if test="$type = 8">
				<xsl:call-template name="extract-text">
					<xsl:with-param name="root"
						select="$texts/*[position() = $subtype]/*/*[position() = ($subsubtype + 1)]//@value" />
					<xsl:with-param name="id" select="0" />
				</xsl:call-template>
			</xsl:if>
			<gfx archive="Object_icons" group="item-id" id="{$id}" position="{$gfxId}" label="{.//*[@name = 'name']/@value}"/>
			<gfx archive="Object_icons" group="item-gfx" id="{$gfxId}" position="{$gfxId}"/>
		</item>
	</xsl:template>

</xsl:stylesheet>