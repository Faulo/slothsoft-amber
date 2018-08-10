<xsl:stylesheet version="1.0"
	xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<xsl:import href="globals/static" />
	
	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame.editor" />
		</amberdata>
	</xsl:template>
	
	<xsl:template match="sse:savegame.editor">
		<xsl:copy-of select="$static/saa:portrait-list"/>
		
		<xsl:for-each select="$static//saa:portrait">
			<gfx archive="Portraits.amb" group="portrait-id" id="{@id}" position="{@id - 1}"/>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>