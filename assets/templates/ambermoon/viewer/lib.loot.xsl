<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />

    <xsl:template match="saa:merchant-list">
        <details class="amber-list__category">
            <summary class="amber-list__title">
                <span class="green">
                    Händler &amp; Bibliotheken
                </span>
            </summary>
            <div class="amber-list__items">
                <xsl:for-each select="*">
                    <div class="amber-list__item">
                        <xsl:apply-templates select="." />
                    </div>
                </xsl:for-each>
            </div>
        </details>
    </xsl:template>

    <xsl:template match="saa:chest-list">
        <details class="amber-list__category">
            <summary class="amber-list__title">
                <span class="green">
                    Truhen &amp; Gerümpel
                </span>
            </summary>
            <div class="amber-list__items">
                <xsl:for-each select="*">
                    <div class="amber-list__item">
                        <xsl:apply-templates select="." />
                    </div>
                </xsl:for-each>
            </div>
        </details>
    </xsl:template>
</xsl:stylesheet>
