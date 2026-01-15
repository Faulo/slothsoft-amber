<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sfx="http://schema.slothsoft.net/farah/xslt">

    <xsl:import href="farah://slothsoft@farah/xsl/xslt" />

    <xsl:template match="saa:class-list">
        <div class="amber-list amber-list--{local-name()}">
            <details class="amber-list__category">
                <summary>Fähigkeiten</summary>
                <div class="amber-list__items">
                    <xsl:for-each select="*">
                        <xsl:sort select="@base-experience" data-type="number" />
                        <div class="amber-list__item">
                            <xsl:apply-templates select="." mode="skills" />
                        </div>
                    </xsl:for-each>
                </div>
            </details>
            <details class="amber-list__category">
                <summary>Erfahrungstabelle</summary>
                <div class="amber-list__items">
                    <xsl:for-each select="*">
                        <xsl:sort select="@base-experience" data-type="number" />
                        <div class="amber-list__item">
                            <xsl:apply-templates select="." mode="experience" />
                        </div>
                    </xsl:for-each>
                </div>
            </details>
        </div>
    </xsl:template>

    <xsl:template match="saa:class" mode="skills">
        <table class="amber-class amber-class--skills">
            <caption>
                <xsl:value-of select="@name" />
            </caption>
            <tbody>
                <xsl:for-each select="saa:skill">
                    <tr class="right-aligned">
                        <td>
                            <xsl:value-of select="@name" />
                            <xsl:text>:</xsl:text>
                        </td>
                        <td class="number">
                            <xsl:value-of select="concat(@maximum, '%')" />
                        </td>
                    </tr>
                </xsl:for-each>
            </tbody>
            <tbody>
                <tr>
                    <th class="yellow smaller" colspan="2">
                        <xsl:choose>
                            <xsl:when test="saa:spellbook-reference">
                                <xsl:for-each select="saa:spellbook-reference">
                                    <p>
                                        <xsl:value-of select="@name" />
                                    </p>
                                </xsl:for-each>
                            </xsl:when>
                            <xsl:otherwise>
                                <p>-</p>
                            </xsl:otherwise>
                        </xsl:choose>
                    </th>
                </tr>
            </tbody>
        </table>
    </xsl:template>

    <xsl:template match="saa:class" mode="experience">
        <xsl:variable name="class" select="." />
        <table class="amber-class amber-class--experience">
            <caption>
                <xsl:value-of select="@name" />
            </caption>
            <thead>
                <tr>
                    <th>level</th>
                    <th>experience</th>
                    <th>lp</th>
                    <th>sp</th>
                    <th>tp</th>
                    <th>slp</th>
                </tr>
            </thead>
            <tbody>
                <xsl:for-each select="sfx:range(1, 50)">
                    <xsl:variable name="lvl" select="." />
                    <tr class="right-aligned">
                        <td class="number green">
                            <xsl:value-of select="$lvl" />
                        </td>
                        <td class="number yellow">
                            <xsl:value-of select="$class/@base-experience * $lvl * ($lvl + 1) div 2" />
                        </td>
                        <td class="number">
                            <xsl:value-of select="$class/@hp-per-level * $lvl" />
                        </td>
                        <td class="number">
                            <xsl:value-of select="$class/@sp-per-level * $lvl" />
                        </td>
                        <td class="number">
                            <xsl:value-of select="$class/@tp-per-level * $lvl" />
                        </td>
                        <td class="number">
                            <xsl:value-of select="$class/@slp-per-level * $lvl" />
                        </td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>
    </xsl:template>
</xsl:stylesheet>
