<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sfx="http://schema.slothsoft.net/farah/xslt">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />

    <xsl:import href="farah://slothsoft@farah/xsl/xslt" />

    <xsl:template match="saa:race-list">
        <div class="amber-list amber-list--{local-name()}">
            <details class="amber-list__category">
                <summary>Rassen</summary>
                <div class="amber-list__items">
                    <xsl:for-each select="*">
                        <div class="amber-list__item">
                            <xsl:apply-templates select="." />
                        </div>
                    </xsl:for-each>
                </div>
            </details>
        </div>
    </xsl:template>

    <xsl:template match="saa:race">
        <div class="amber-race amber-text">
            <table class="amber-table amber-table--numbers amber-table--attributes" data-ch="13">
                <caption class="amber-table__label amber-text amber-text--green">
                    <xsl:value-of select="@name" />
                </caption>
                <tbody>
                    <xsl:for-each select="saa:attribute">
                        <tr>
                            <th>
                                <xsl:value-of select="@name" />
                            </th>
                            <td>
                                <xsl:value-of select="@maximum" />
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="amber-table__footer  amber-text amber-text--silver" colspan="2">
                            <xsl:value-of select="concat('Höchstalter ', saa:age/@maximum)" />
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </xsl:template>

    <xsl:template match="saa:class-list">
        <div class="amber-list amber-list--{local-name()}">
            <details class="amber-list__category">
                <summary>Klassen</summary>
                <div class="amber-list__items">
                    <xsl:for-each select="*">
                        <div class="amber-list__item">
                            <xsl:apply-templates select="." mode="skills" />
                        </div>
                    </xsl:for-each>
                </div>
            </details>
            <details class="amber-list__category">
                <summary>Erfahrungstabellen</summary>
                <div class="amber-list__items">
                    <xsl:for-each select="*">
                        <div class="amber-list__item">
                            <xsl:apply-templates select="." mode="experience" />
                        </div>
                    </xsl:for-each>
                </div>
            </details>
        </div>
    </xsl:template>

    <xsl:template match="saa:class" mode="skills">
        <div class="amber-class amber-class--skills amber-text">
            <table class="amber-table amber-table--numbers amber-table--skills" data-ch="13">
                <caption class="amber-table__label amber-text amber-text--green">
                    <xsl:value-of select="@name" />
                </caption>
                <tbody>
                    <xsl:for-each select="saa:skill">
                        <tr>
                            <th>
                                <xsl:value-of select="@name" />
                            </th>
                            <td>
                                <xsl:value-of select="concat(@maximum, '%')" />
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="amber-table__footer amber-text amber-text--orange" colspan="2">
                            <xsl:choose>
                                <xsl:when test="saa:spellbook-reference">
                                    <xsl:for-each select="saa:spellbook-reference">
                                        <xsl:value-of select="@name" />
                                    </xsl:for-each>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>-</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </xsl:template>

    <xsl:template match="saa:class" mode="experience">
        <xsl:variable name="class" select="." />
        <div class="amber-class amber-class--experience amber-text">
            <table class="amber-table amber-table--numbers">
                <caption class="amber-table__label amber-text amber-text--green">
                    <xsl:value-of select="@name" />
                </caption>
                <thead>
                    <tr>
                        <th class="amber-text amber-text--blue">level</th>
                        <th class="amber-text amber-text--blue">Erfahrung</th>
                        <th class="amber-text amber-text--blue" data-ch="3">lp</th>
                        <th class="amber-text amber-text--blue" data-ch="3">sp</th>
                        <th class="amber-text amber-text--blue" data-ch="3">tp</th>
                        <th class="amber-text amber-text--blue" data-ch="3">slp</th>
                    </tr>
                </thead>
                <tbody>
                    <xsl:for-each select="sfx:range(1, 50)">
                        <xsl:variable name="lvl" select="." />
                        <tr>
                            <td class="amber-text amber-text--yellow">
                                <xsl:value-of select="$lvl" />
                            </td>
                            <td class="amber-text">
                                <xsl:value-of select="$class/@base-experience * $lvl * ($lvl + 1) div 2" />
                            </td>
                            <td class="amber-text amber-text--silver">
                                <xsl:value-of select="$class/@hp-per-level * $lvl" />
                            </td>
                            <td class="amber-text">
                                <xsl:value-of select="$class/@sp-per-level * $lvl" />
                            </td>
                            <td class="amber-text amber-text--silver">
                                <xsl:value-of select="$class/@tp-per-level * $lvl" />
                            </td>
                            <td class="amber-text">
                                <xsl:value-of select="$class/@slp-per-level * $lvl" />
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </div>
    </xsl:template>
</xsl:stylesheet>
