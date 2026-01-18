<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:html="http://www.w3.org/1999/xhtml" xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:str="http://exslt.org/strings" xmlns:sfx="http://schema.slothsoft.net/farah/xslt" extension-element-prefixes="str">

    <xsl:import href="farah://slothsoft@farah/xsl/xslt" />

    <xsl:key name="dictionary-option" match="//saa:dictionary/saa:option" use="../@dictionary-id" />

    <xsl:template match="sfm:fragment-info">
        <div>
            <xsl:apply-templates select="sfm:document-info[1]/saa:amberdata" />
        </div>
    </xsl:template>


    <xsl:template match="saa:amberdata">
        <xsl:apply-templates select="*" />
    </xsl:template>

    <xsl:template match="*">
        <h1>UNKNOWN AMBER DATA</h1>
        <xsl:copy-of select="." />
    </xsl:template>

    <xsl:template name="amber-list">
        <div class="amber-list amber-list--{local-name()}">
            <xsl:apply-templates select="*" />
        </div>
    </xsl:template>

    <xsl:template name="amber-category">
        <xsl:if test="*">
            <details class="amber-list__category">
                <summary class="amber-list__title">
                    <xsl:text>Kategorie: </xsl:text>
                    <span class="green">
                        <xsl:value-of select="@name" />
                    </span>
                </summary>
                <div class="amber-list__items">
                    <xsl:for-each select="*">
                        <xsl:sort select="saa:class-instance/@experience" data-type="number" />
                        <xsl:sort select="@level" data-type="number" />
                        <div class="amber-list__item">
                            <xsl:apply-templates select="." />
                        </div>
                    </xsl:for-each>
                </div>
            </details>
        </xsl:if>
    </xsl:template>

    <xsl:template match="saa:spellbook-list">
        spellbook-list
    </xsl:template>

    <xsl:template match="saa:map-list">
        <xsl:choose>
            <xsl:when test="count(*) = 1">
                <xsl:for-each select="*">
                    <button type="button" onclick="savegameEditor.viewMap(this.parentNode, '{@id}')">
                        Weltkarte generieren:
                        <span class="green">
                            <xsl:value-of select="@name" />
                        </span>
                    </button>
                    <template>
                        <xsl:copy-of select="." />
                    </template>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="*">
                    <details ontoggle="savegameEditor.viewMap(this, '{@id}')">
                        <summary>
                            <h2>
                                Karte:
                                <span class="green">
                                    <xsl:value-of select="@name" />
                                </span>
                            </h2>
                        </summary>
                        <template>
                            <xsl:copy-of select="." />
                        </template>
                    </details>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template match="saa:class-instance" mode="itemlist-inline">
        <table class="ClassData">
            <tbody>
                <xsl:for-each select="saa:skill">
                    <tr class="right-aligned">
                        <td>
                            <xsl:value-of select="@name" />
                            <xsl:text>:</xsl:text>
                        </td>
                        <td class="number">
                            <xsl:value-of select="concat(@current, '%')" />
                        </td>
                        <td>
                            <xsl:text>/</xsl:text>
                        </td>
                        <td class="number">
                            <xsl:value-of select="concat(@maximum, '%')" />
                        </td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>
    </xsl:template>

    <xsl:template match="saa:race">
        <article data-race-id="{@id}" data-template="flex" class="Race">
            <xsl:value-of select="@name" />
            <xsl:apply-templates select="." mode="itemlist-inline" />
        </article>
    </xsl:template>

    <xsl:template match="saa:race" mode="itemlist-inline">
        <table class="RaceData">
            <tbody>
                <xsl:for-each select="saa:attribute">
                    <tr class="right-aligned">
                        <td>
                            <xsl:value-of select="@name" />
                            <xsl:text>:</xsl:text>
                        </td>
                        <xsl:if test="@current">
                            <td class="number">
                                <xsl:value-of select="concat(format-number(@current, '###'), '')" />
                            </td>
                            <td>
                                <xsl:text>/</xsl:text>
                            </td>
                        </xsl:if>
                        <td class="number">
                            <xsl:value-of select="concat(@maximum, '')" />
                        </td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="saa:monster | saa:npc | saa:pc">
        <xsl:variable name="isMage" select="saa:class-instance/saa:sp/@maximum &gt; 0" />
        <article data-monster-id="{@id}" class="Character">
            <table>
                <tr>
                    <td class="attributes">
                        <h3>attributes</h3>
                        <xsl:apply-templates select="saa:race" mode="itemlist-inline" />
                    </td>
                    <td class="languages">
                        <h3>languages</h3>
                        <xsl:variable name="languages" select="saa:language/@name" />
                        <table>
                            <xsl:for-each select="key('dictionary-option', 'languages')">
                                <tr>
                                    <td>
                                        <xsl:if test="@val = $languages">
                                            <xsl:value-of select="@val" />
                                        </xsl:if>
                                        <xsl:text>&#160;</xsl:text>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </td>
                    <td class="skills">
                        <h3>skills</h3>
                        <xsl:apply-templates select="saa:class-instance" mode="itemlist-inline" />
                    </td>
                    <td class="character-sheet">
                        <table>
                            <tr>
                                <td rowspan="5">
                                    <xsl:apply-templates select="." mode="itemlist-picture" />
                                </td>
                                <td>
                                    <xsl:value-of select="saa:race/@name" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="concat(@gender, ' ')" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    age:
                                    <xsl:value-of select="saa:race/saa:age/@current" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="saa:class-instance/@name" />
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="@level" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    ep:
                                    <xsl:value-of select="saa:class-instance/@experience" />
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td colspan="2" class="yellow">
                                    <xsl:value-of select="@name" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    lp:
                                    <xsl:value-of select="concat(saa:class-instance/saa:hp/@current, '/', saa:class-instance/saa:hp/@maximum)" />
                                </td>
                                <td>
                                    <xsl:if test="$isMage">
                                        sp:
                                        <xsl:value-of select="concat(saa:class-instance/saa:sp/@current, '/', saa:class-instance/saa:sp/@maximum)" />
                                    </xsl:if>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    tp:
                                    <xsl:value-of select="@training-points" />
                                </td>
                                <td>
                                    <xsl:if test="$isMage">
                                        slp:
                                        <xsl:value-of select="@spelllearn-points" />
                                    </xsl:if>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    gold:
                                    <xsl:value-of select="@gold" />
                                </td>
                                <td>
                                    food:
                                    <xsl:value-of select="@food" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    attack:
                                    <xsl:value-of select="@attack" />
                                </td>
                                <td>
                                    defense:
                                    <xsl:value-of select="@defense" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    m-b-w:
                                    <xsl:value-of select="@magic-attack" />
                                </td>
                                <td>
                                    m-b-r:
                                    <xsl:value-of select="@magic-defense" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    APR:
                                    <xsl:value-of select="@attacks-per-round" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <xsl:if test="saa:equipment and saa:inventory">
                <table>
                    <tr>
                        <td>
                            <h3>equipment</h3>
                            <xsl:apply-templates select="saa:equipment" mode="itemlist-inline" />
                        </td>
                        <td>
                            <h3>inventory</h3>
                            <xsl:apply-templates select="saa:inventory" mode="itemlist-inline" />
                            <xsl:if test="saa:class-instance/saa:spellbook-instance/*">
                                <h3>spells</h3>
                                <xsl:apply-templates select="saa:class-instance/saa:spellbook-instance[*]" mode="itemlist-inline" />
                            </xsl:if>
                        </td>
                    </tr>
                </table>
            </xsl:if>
            <xsl:if test="saa:event">
                <div>
                    <h3>dialog</h3>
                    <xsl:for-each select="saa:event">
                        <details>
                            <summary class="textlabel">
                                <xsl:for-each select="saa:trigger">
                                    <xsl:value-of select="@name" />
                                    <xsl:if test="@value">
                                        <xsl:text>: </xsl:text>
                                        <span class="yellow">
                                            <xsl:value-of select="@value" />
                                        </span>
                                    </xsl:if>
                                </xsl:for-each>
                            </summary>
                            <xsl:for-each select="saa:text">
                                <div class="textbox">
                                    <xsl:for-each select="saa:paragraph">
                                        <p>
                                            <xsl:value-of select="normalize-space(.)" />
                                        </p>
                                    </xsl:for-each>
                                </div>
                            </xsl:for-each>
                        </details>
                    </xsl:for-each>
                </div>
            </xsl:if>
        </article>
    </xsl:template>

    <xsl:template match="saa:monster" mode="itemlist-picture">
        <amber-picker infoset="lib.monsters" type="monster" role="button" tabindex="0">
            <amber-monster-id value="{@id}" />
        </amber-picker>
    </xsl:template>

    <xsl:template match="saa:pc | saa:npc" mode="itemlist-picture">
        <amber-picker infoset="lib.portraits" type="portrait" role="button" tabindex="0">
            <amber-portrait-id value="{@portrait-id}" />
        </amber-picker>
    </xsl:template>

    <xsl:template match="saa:spellbook-instance" mode="itemlist-inline">
        <div class="spells">
            <h3 class="yellow">
                <xsl:value-of select="@name" />
            </h3>
            <ul>
                <xsl:for-each select="saa:spell-reference">
                    <li>
                        <xsl:value-of select="@name" />
                    </li>
                </xsl:for-each>
            </ul>
        </div>
    </xsl:template>

    <xsl:template match="saa:equipment" mode="itemlist-inline">
        <xsl:variable name="options" select="key('dictionary-option', 'equipment-slots')" />
        <div class="equipment" data-template="flex">
            <ul>
                <xsl:for-each select="saa:slot">
                    <xsl:variable name="pos" select="position()" />
                    <li>
                        <xsl:apply-templates select="." mode="itemlist-inline" />
                        <span class="name">
                            <xsl:value-of select="$options[$pos]/@val" />
                        </span>
                    </li>
                </xsl:for-each>
            </ul>
        </div>
    </xsl:template>

    <xsl:template match="saa:inventory" mode="itemlist-inline">
        <div class="inventory" data-template="flex">
            <ul>
                <xsl:for-each select="saa:slot">
                    <li>
                        <xsl:apply-templates select="." mode="itemlist-inline" />
                    </li>
                </xsl:for-each>
            </ul>
        </div>
    </xsl:template>

    <xsl:template match="saa:slot" mode="itemlist-inline">
        <amber-picker infoset="lib.items" type="item" role="button" tabindex="0" onclick="savegameEditor.openPopup(arguments[0])">
            <amber-item-id value="{saa:item-instance/@item-id}" />
            <amber-item-amount value="{saa:item-instance/@item-amount}" />
            <amber-item-charge value="{saa:item-instance/@item-charge}" />
        </amber-picker>
        <xsl:if test="@name">
            <span class="name">
                <xsl:value-of select="@name" />
            </span>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
