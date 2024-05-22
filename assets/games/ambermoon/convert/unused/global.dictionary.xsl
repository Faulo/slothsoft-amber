<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:str="http://exslt.org/strings"
	xmlns:set="http://exslt.org/sets" xmlns:math="http://exslt.org/math" xmlns:php="http://php.net/xsl"
	xmlns:save="http://schema.slothsoft.net/savegame/editor" xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:html="http://www.w3.org/1999/xhtml" extension-element-prefixes="exsl func str set math php">

	<xsl:variable name="dataDocument" select="/*/*[@name='dataset']/sse:savegame.editor" />
	<xsl:variable name="dictionaryDocument" select="/*/*[@name='dictionaries']/saa:amberdata" />

	<xsl:template match="/*/*[@name='dataset']">
		<amberdata version="0.1">
			<xsl:apply-templates select="sse:savegame.editor" />
		</amberdata>
	</xsl:template>

	<xsl:key name="dictionary-option" match="saa:amberdata/saa:dictionary-list/saa:dictionary/saa:option"
		use="../@dictionary-id" />
	<xsl:key name="string-dictionary" match="sse:instruction[@type='string-dictionary']/sse:string" use="../@name" />

	<func:function name="saa:getName">
		<xsl:param name="context" select="." />

		<xsl:choose>
			<xsl:when test="@name">
				<func:result select="string(@name)" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="../@dictionary-ref">
					<xsl:variable name="option"
						select="saa:getDictionaryOption(../@dictionary-ref, count(preceding-sibling::*))" />
					<func:result select="string($option/@val)" />
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</func:function>

	<func:function name="saa:getDictionaryOption">
		<xsl:param name="id" />
		<xsl:param name="key" />

		<func:result select="saa:getDictionary($id)[@key = $key]" />
	</func:function>

	<func:function name="saa:getDictionary">
		<xsl:param name="id" />

		<xsl:choose>
			<xsl:when test="$dictionaryDocument">
				<xsl:for-each select="$dictionaryDocument">
					<func:result select="key('dictionary-option', $id)" />
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<func:result select="/.." />
			</xsl:otherwise>
		</xsl:choose>
	</func:function>

	<xsl:template match="sse:integer | sse:signed-integer | sse:string" mode="attr">
		<xsl:param name="name" select="@name" />
		<xsl:param name="value" select="@value" />
		<xsl:attribute name="{$name}"><xsl:value-of select="normalize-space($value)" /></xsl:attribute>
	</xsl:template>

	<xsl:template match="sse:select" mode="attr">
		<xsl:param name="name" select="@name" />
		<xsl:variable name="option" select="saa:getDictionaryOption(@dictionary-ref, @value)" />
		<xsl:attribute name="{$name}">
			<xsl:value-of select="$option/@title | $option/@val[not($option/@title)]" />
		</xsl:attribute>
	</xsl:template>

	<xsl:template match="sse:group" mode="unknown">
		<unknown>
			<xsl:for-each select="*">
				<xsl:if test="position() &gt; 1">
					<xsl:text> </xsl:text>
				</xsl:if>
				<xsl:value-of select="@value" />
				<!-- <xsl:value-of select="str:align(@value, '000', 'right')"/> -->
			</xsl:for-each>
		</unknown>
	</xsl:template>

</xsl:stylesheet>