<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
    xmlns:sse="http://schema.slothsoft.net/savegame/editor">
    
    <xsl:template match="sse:archive">
        <h1><xsl:value-of select="@name"/></h1>
    </xsl:template>

</xsl:stylesheet>