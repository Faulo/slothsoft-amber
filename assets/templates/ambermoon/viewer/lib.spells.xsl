<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />


    <xsl:template match="saa:spellbook-list">
        <xsl:call-template name="amber-list" />
    </xsl:template>

    <xsl:template match="saa:spellbook">
        <xsl:call-template name="amber-category" />
    </xsl:template>

    <xsl:template match="saa:spell">
        <article class="amber-spell amber-text">
            <div class="amber-spell__name amber-text amber-text--yellow">
                <xsl:value-of select="@name" />
            </div>
            <div class="amber-spell__data">
                <table class="amber-table amber-table--numbers">
                    <caption class="amber-text amber-text--green">Kosten</caption>
                    <tbody>
                        <tr>
                            <td class="amber-text amber-text--silver">
                                <xsl:text>SP:</xsl:text>
                            </td>
                            <td>
                                <xsl:value-of select="@sp" />
                            </td>
                        </tr>
                        <tr>
                            <td class="amber-text amber-text--silver">
                                <xsl:text>SLP:</xsl:text>
                            </td>
                            <td>
                                <xsl:value-of select="@slp" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="amber-table amber-table--numbers">
                    <caption class="amber-text amber-text--green">Wirkung</caption>
                    <tbody>
                        <tr>
                            <td class="amber-text amber-text--silver amber-text--right">
                                <xsl:text>Ziel:</xsl:text>
                            </td>
                            <td>
                                <xsl:value-of select="@spell-target" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <xsl:copy-of select="." />
        </article>
    </xsl:template>
</xsl:stylesheet>
