<xsl:stylesheet version="1.0" 
	xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func">

	<xsl:variable name="dictionaryDocument" select="/*/*/saa:amberdata" />

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

</xsl:stylesheet>