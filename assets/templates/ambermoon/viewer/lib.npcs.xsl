<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />

    <xsl:template match="saa:npc-list">
        <xsl:call-template name="amber-list" />
    </xsl:template>

    <xsl:template match="saa:npc-category">
        <xsl:call-template name="amber-category" />
    </xsl:template>
</xsl:stylesheet>
