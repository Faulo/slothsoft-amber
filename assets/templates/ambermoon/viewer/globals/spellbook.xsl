<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:html="http://www.w3.org/1999/xhtml" xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:str="http://exslt.org/strings" xmlns:sfx="http://schema.slothsoft.net/farah/xslt" extension-element-prefixes="str">

    <xsl:variable name="spellbooks" select="document('farah://slothsoft@amber/api/amberdata?infosetId=lib.spells')/saa:amberdata/saa:spellbook-list" />

    <xsl:template match="saa:spellbook-instance" mode="character" priority="10">
        <xsl:param name="sp" select="/.." />
        <xsl:variable name="spellbook" select="$spellbooks/saa:spellbook[@id = current()/@id]" />
        <ul>
            <xsl:for-each select="saa:spell-reference">
                <xsl:variable name="spell" select="$spellbook/saa:spell[@id = current()/@id]" />
                <li class="amber-text amber-text--spellbook-{../@id}">
                    <xsl:value-of select="@name" />
                    <xsl:if test="$sp">
                        <xsl:text> (</xsl:text>
                        <xsl:value-of select="floor($sp div $spell/@sp)" />
                        <xsl:text>)</xsl:text>
                    </xsl:if>
                </li>
            </xsl:for-each>
        </ul>
    </xsl:template>
</xsl:stylesheet>
