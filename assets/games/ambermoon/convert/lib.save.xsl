<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="sse:savegame">
		<xsl:copy-of select="." />
	</xsl:template>

</xsl:stylesheet>