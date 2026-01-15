<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />

    <xsl:template match="saa:item-list">
        <xsl:call-template name="amber-list" />
    </xsl:template>

    <xsl:template match="saa:item-category">
        <xsl:call-template name="amber-category" />
    </xsl:template>

    <xsl:template match="saa:item">
        <!--<item xmlns="" id="361" image="9" name="MAGIERSTIEFEL" type="Schuhe" hands="0" fingers="0" damage="0" armor="6" weight="850" gender="beide" class="Magier Mystik. Alchem. Heiler"/> -->
        <article data-item-id="{@id}" class="amber-item">
            <table>
                <tbody class="amber-item__name">
                    <tr>
                        <td rowspan="2">
                            <amber-picker infoset="lib.items" type="item" role="button" tabindex="0">
                                <amber-item-gfx value="{@image-id}" />
                            </amber-picker>
                        </td>
                        <th colspan="2">
                            <xsl:value-of select="@name" />
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <xsl:value-of select="@type" />
                        </td>
                    </tr>
                </tbody>
                <tbody class="amber-item__worth">
                    <tr class="right-aligned">
                        <td colspan="2">Gewicht:</td>
                        <td class="number">
                            <xsl:value-of select="concat(@weight, ' gr')" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td colspan="2">Wert:</td>
                        <td class="number">
                            <xsl:value-of select="concat(@price, ' gp')" />
                        </td>
                    </tr>
                </tbody>
                <tbody class="amber-item__stats">
                    <tr class="right-aligned">
                        <td colspan="2">Hände:</td>
                        <td class="number">
                            <xsl:value-of select="@hands" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td colspan="2">Finger:</td>
                        <td class="number">
                            <xsl:value-of select="@fingers" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td colspan="2">Schaden:</td>
                        <td class="number">
                            <xsl:if test="@damage &gt; 0">
                                <xsl:text>+</xsl:text>
                            </xsl:if>
                            <xsl:value-of select="@damage" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td colspan="2">Schutz:</td>
                        <td class="number">
                            <xsl:if test="@armor &gt; 0">
                                <xsl:text>+</xsl:text>
                            </xsl:if>
                            <xsl:value-of select="@armor" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <xsl:choose>
                <xsl:when test="saa:text">
                    <div class="amber-item__textbox">
                        <xsl:for-each select="saa:text/saa:paragraph">
                            <p>
                                <xsl:value-of select="normalize-space(.)" />
                            </p>
                        </xsl:for-each>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <table>
                        <tbody class="amber-item__classes">
                            <tr>
                                <td class="gray">------ Klassen ------</td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:for-each select="saa:class-reference/@name">
                                        <span class="amber-item__class-reference">
                                            <xsl:value-of select="." />
                                        </span>
                                    </xsl:for-each>
                                </td>
                            </tr>
                        </tbody>
                        <tbody class="amber-item__gender">
                            <tr>
                                <td class="gray">Geschlecht</td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="@gender" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="amber-item__magic">
                        <tbody>
                            <tr>
                                <td class="right-aligned" data-hover-text="Lebenspunkte Maximum">LP-Max: </td>
                                <td class="number">
                                    <xsl:if test="@lp-max &gt; 0">
                                        <xsl:choose>
                                            <xsl:when test="@is-cursed">
                                                -
                                            </xsl:when>
                                            <xsl:otherwise>
                                                +
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:if>
                                    <xsl:value-of select="@lp-max" />
                                </td>
                                <td class="right-aligned" data-hover-text="Spruchpunkte Maximum">SP-Max: </td>
                                <td class="number">
                                    <xsl:if test="@sp-max &gt; 0">
                                        <xsl:choose>
                                            <xsl:when test="@is-cursed">
                                                -
                                            </xsl:when>
                                            <xsl:otherwise>
                                                +
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:if>
                                    <xsl:value-of select="@sp-max" />
                                </td>
                            </tr>
                            <tr>
                                <td class="right-aligned" data-hover-text="Magischer Rüstschutz, Angriff">M-B-W: </td>
                                <td class="number">
                                    <xsl:if test="@magic-weapon &gt; 0">
                                        <xsl:choose>
                                            <xsl:when test="@is-cursed">
                                                -
                                            </xsl:when>
                                            <xsl:otherwise>
                                                +
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:if>
                                    <xsl:value-of select="@magic-weapon" />
                                </td>
                                <td class="right-aligned" data-hover-text="Magischer Rüstschutz, Verteidigung">M-B-R: </td>
                                <td class="number">
                                    <xsl:if test="@magic-armor &gt; 0">
                                        <xsl:choose>
                                            <xsl:when test="@is-cursed">
                                                -
                                            </xsl:when>
                                            <xsl:otherwise>
                                                +
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:if>
                                    <xsl:value-of select="@magic-armor" />
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="4" class="orange">
                                    <xsl:if test="@attribute-value &gt; 0">
                                        Attribut
                                    </xsl:if>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <xsl:if test="@attribute-value &gt; 0">
                                        <xsl:value-of select="@attribute-type" />
                                    </xsl:if>
                                </td>
                                <td class="number">
                                    <xsl:if test="@attribute-value &gt; 0">
                                        <xsl:choose>
                                            <xsl:when test="@is-cursed">
                                                -
                                            </xsl:when>
                                            <xsl:otherwise>
                                                +
                                            </xsl:otherwise>
                                        </xsl:choose>
                                        <xsl:value-of select="@attribute-value" />
                                    </xsl:if>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="4" class="orange">
                                    <xsl:if test="@skill-value &gt; 0">
                                        Fähigkeit
                                    </xsl:if>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <xsl:if test="@skill-value &gt; 0">
                                        <xsl:value-of select="@skill-type" />
                                    </xsl:if>
                                </td>
                                <td class="number">
                                    <xsl:if test="@skill-value &gt; 0">
                                        <xsl:choose>
                                            <xsl:when test="@is-cursed">
                                                -
                                            </xsl:when>
                                            <xsl:otherwise>
                                                +
                                            </xsl:otherwise>
                                        </xsl:choose>
                                        <xsl:value-of select="@skill-value" />
                                    </xsl:if>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="4" class="orange">
                                    <xsl:if test="@spell-id &gt; 0">
                                        <xsl:value-of select="@spell-type" />
                                    </xsl:if>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <xsl:if test="@spell-id &gt; 0">
                                        <xsl:value-of select="concat(@spell-name, ' (', @charges-default, ')')" />
                                    </xsl:if>
                                    <xsl:if test="@is-cursed">
                                        <span class="green">verflucht</span>
                                    </xsl:if>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </xsl:otherwise>
            </xsl:choose>
        </article>
    </xsl:template>
</xsl:stylesheet>
