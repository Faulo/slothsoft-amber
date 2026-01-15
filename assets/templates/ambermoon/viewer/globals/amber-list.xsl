<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:html="http://www.w3.org/1999/xhtml" xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:str="http://exslt.org/strings" xmlns:sfx="http://schema.slothsoft.net/farah/xslt" extension-element-prefixes="str">

    <xsl:import href="farah://slothsoft@farah/xsl/xslt" />

    <xsl:variable name="amberdata" select="/*/*[@name='amberdata']/saa:amberdata" />

    <xsl:key name="dictionary-option" match="/*/*[@name='dictionaries']/saa:amberdata/saa:dictionary-list/saa:dictionary/saa:option" use="../@dictionary-id" />

    <xsl:template match="saa:amberdata">
        <div>
            <xsl:apply-templates select="*" />
        </div>
    </xsl:template>

    <xsl:template match="*">
        <h1>UNKNOWN AMBER DATA</h1>
        <xsl:copy-of select="." />
    </xsl:template>

    <xsl:template match="saa:portrait-instance-list | saa:gfx-list" />

    <xsl:template match="saa:portrait-list | saa:item-list | saa:monster-list | saa:npc-list | saa:pc-list">
        <div class="amber-list amber-list--{local-name()}">
            <xsl:apply-templates select="*" />
        </div>
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

    <xsl:template match="saa:portrait-category | saa:item-category | saa:monster-category | saa:npc-category | saa:pc-category">
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

    <xsl:template match="saa:portrait">
        <article data-portrait-id="{@id}" class="amber-portrait">
            <amber-picker infoset="lib.portraits" type="portrait" role="button" tabindex="0">
                <amber-portrait-id value="{@id}" />
            </amber-picker>
            <div class="amber-portrait__name">
                <xsl:value-of select="@name" />
            </div>
            <xsl:for-each select="//saa:portrait-instance[@id = current()/@id]">
                <div class="amber-portrait__character">
                    <xsl:value-of select="@character" />
                </div>
            </xsl:for-each>

        </article>
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
                        <th>
                            <xsl:value-of select="@name" />
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <xsl:value-of select="@type" />
                        </td>
                    </tr>
                </tbody>
                <tbody class="amber-item__worth">
                    <tr class="right-aligned">
                        <td>Gewicht:</td>
                        <td class="number">
                            <xsl:value-of select="concat(@weight, ' gr')" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td>Wert:</td>
                        <td class="number">
                            <xsl:value-of select="concat(@price, ' gp')" />
                        </td>
                    </tr>
                </tbody>
                <tbody class="amber-item__stats">
                    <tr class="right-aligned">
                        <td>Hände:</td>
                        <td class="number">
                            <xsl:value-of select="@hands" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td>Finger:</td>
                        <td class="number">
                            <xsl:value-of select="@fingers" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td>Schaden:</td>
                        <td class="number">
                            <xsl:if test="@damage &gt; 0">
                                <xsl:text>+</xsl:text>
                            </xsl:if>
                            <xsl:value-of select="@damage" />
                        </td>
                    </tr>
                    <tr class="right-aligned">
                        <td>Schutz:</td>
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
