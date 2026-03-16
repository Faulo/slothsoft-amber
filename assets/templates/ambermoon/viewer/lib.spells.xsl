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
        <xsl:variable name="places" select="*" />
        <article class="amber-spell amber-text">
            <div class="amber-spell__name amber-text amber-text--yellow">
                <xsl:value-of select="@name" />
            </div>
            <div class="amber-spell__data">
                <h3 class="amber-text amber-text--green amber-text--caption">Einsatzgebiete</h3>
                <div class="amber-spell__list">
                    <xsl:variable name="all-places" select="key('dictionary-option', 'spell-places')" />
                    <ul>
                        <xsl:for-each select="$all-places[1] | $all-places[2] | $all-places[3]">
                            <li>
                                <xsl:choose>
                                    <xsl:when test="$places[@name = current()/@val]">
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">amber-text amber-text--disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:value-of select="@val" />
                            </li>
                        </xsl:for-each>
                    </ul>
                    <ul>
                        <xsl:for-each select="$all-places[4] | $all-places[5]">
                            <li>
                                <xsl:choose>
                                    <xsl:when test="$places[@name = current()/@val]">
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">amber-text amber-text--disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:value-of select="@val" />
                            </li>
                        </xsl:for-each>
                    </ul>
                    <ul>
                        <xsl:for-each select="$all-places[6] | $all-places[7] | $all-places[8]">
                            <li>
                                <xsl:choose>
                                    <xsl:when test="$places[@name = current()/@val]">
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:attribute name="class">amber-text amber-text--disabled</xsl:attribute>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:value-of select="@val" />
                            </li>
                        </xsl:for-each>
                    </ul>
                </div>
            </div>
            <div class="amber-spell__data amber-spell__list">
                <table class="amber-table amber-table--numbers">
                    <caption class="amber-text amber-text--green amber-text--caption">Kosten</caption>
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
                    <caption class="amber-text amber-text--green amber-text--caption">Wirkung</caption>
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
        </article>
    </xsl:template>
</xsl:stylesheet>
