<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:str="http://exslt.org/strings"
	xmlns:set="http://exslt.org/sets" xmlns:math="http://exslt.org/math" xmlns:php="http://php.net/xsl"
	xmlns:save="http://schema.slothsoft.net/savegame/editor" xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:html="http://www.w3.org/1999/xhtml" extension-element-prefixes="exsl func str set math php">
	
	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame.editor" />
		</amberdata>
	</xsl:template>
	
	<xsl:template match="sse:savegame.editor" />

</xsl:stylesheet>