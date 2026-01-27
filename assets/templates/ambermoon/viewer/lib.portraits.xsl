<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />

    <xsl:template match="saa:portrait-instance-list | saa:gfx-list" />

    <xsl:template match="saa:portrait-list">
        <xsl:call-template name="amber-list" />
    </xsl:template>

    <xsl:template match="saa:portrait-category">
        <xsl:call-template name="amber-category" />
    </xsl:template>

    <xsl:template match="saa:portrait">
        <article data-portrait-id="{@id}" class="amber-portrait">
            <div class="amber-portrait__id amber-text amber-text--yellow">
                <xsl:text>Portrait #</xsl:text>
                <xsl:value-of select="format-number(@id, '000')" />
            </div>
            <amber-embed infoset="lib.portraits" type="portrait" role="button" tabindex="0">
                <amber-portrait-id value="{@id}" />
            </amber-embed>
            <div class="amber-portrait__name amber-text amber-text--silver">
                <xsl:for-each select="//saa:portrait-instance[@id = current()/@id]">
                    <div class="amber-portrait__character">
                        <xsl:value-of select="@character" />
                    </div>
                </xsl:for-each>
            </div>
        </article>
    </xsl:template>
</xsl:stylesheet>
