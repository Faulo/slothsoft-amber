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


    <xsl:template match="saa:class-instance" mode="character">
        <xsl:variable name="skills" select="key('dictionary-option', 'skills')" />
        <table class="amber-character__skills">
            <caption class="amber-text amber-text--green">fähigkeiten</caption>
            <tbody>
                <xsl:for-each select="saa:skill">
                    <tr>
                        <td data-hover-text="{$skills[@val = current()/@name]/@title}">
                            <xsl:value-of select="@name" />
                        </td>
                        <td>
                            <xsl:value-of select="concat(@current, '%')" />
                        </td>
                        <td>
                            <xsl:text>/</xsl:text>
                        </td>
                        <td>
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

    <xsl:template match="saa:race" mode="character">
        <xsl:variable name="attributes" select="key('dictionary-option', 'attributes')" />
        <table class="amber-character__attributes">
            <caption class="amber-text amber-text--green">attribute</caption>
            <tbody>
                <xsl:for-each select="saa:attribute">
                    <tr class="right-aligned">
                        <td data-hover-text="{$attributes[@val = current()/@name]/@title}">
                            <xsl:value-of select="@name" />
                        </td>
                        <xsl:if test="@current">
                            <td>
                                <xsl:value-of select="@current" />
                            </td>
                            <td>
                                <xsl:text>/</xsl:text>
                            </td>
                        </xsl:if>
                        <td>
                            <xsl:value-of select="@maximum" />
                        </td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="saa:monster | saa:npc | saa:pc">
        <xsl:variable name="isMage" select="saa:class-instance/saa:sp/@maximum &gt; 0" />
        <article data-character-id="{@id}" class="amber-character amber-character--{local-name()} amber-text">

            <div class="amber-character__tables">
                <div class="amber-character__body">
                    <div class="amber-character__tables">
                        <xsl:apply-templates select="saa:race" mode="character" />
                        <xsl:variable name="languages" select="saa:language/@name" />
                        <table class="amber-character__languages">
                            <caption class="amber-text amber-text--green">sprachen</caption>
                            <tbody>
                                <xsl:for-each select="key('dictionary-option', 'languages')">
                                    <tr>
                                        <td>
                                            <xsl:if test="@val = $languages">
                                                <xsl:value-of select="@val" />
                                            </xsl:if>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>
                    <xsl:if test="saa:equipment">
                        <div class="amber-character__equipment">
                            <h3 class="amber-text amber-text--green">ausrüstung</h3>
                            <xsl:apply-templates select="saa:equipment" mode="character" />
                        </div>
                    </xsl:if>
                </div>
                <div>
                    <div class="amber-character__tables">
                        <div>
                            <xsl:apply-templates select="saa:class-instance" mode="character" />
                            <xsl:if test="saa:equipment">
                                <div class="amber-character__apr">
                                    <span data-hover-text="Attacken pro Runde">APR:</span>
                                    <xsl:value-of select="@attacks-per-round" />
                                    <span data-hover-text="Weitere APR alle {saa:class-instance/@apr-per-level} Stufen">
                                        <xsl:value-of select="concat(' (', saa:class-instance/@apr-per-level, ')')" />
                                    </span>
                                </div>
                            </xsl:if>
                        </div>
                        <div class="amber-character__sheet">
                            <div class="amber-character__tables">
                                <xsl:apply-templates select="." mode="character-picture" />
                                <ul class="amber-character__basic-info">
                                    <li>
                                        <xsl:value-of select="saa:race/@name" />
                                    </li>
                                    <li>
                                        <xsl:value-of select="@gender" />
                                    </li>
                                    <li>
                                        <xsl:text>alter:</xsl:text>
                                        <xsl:value-of select="saa:race/saa:age/@current" />
                                    </li>
                                    <li>
                                        <xsl:value-of select="saa:class-instance/@name" />
                                        <xsl:text> </xsl:text>
                                        <xsl:value-of select="@level" />
                                    </li>
                                    <li>
                                        <xsl:text>ep:</xsl:text>
                                        <xsl:value-of select="saa:class-instance/@experience" />
                                    </li>
                                    <li>
                                        <span class="amber-icon amber-icon--gold" data-hover-text="Gold">:</span>
                                        <xsl:value-of select="@gold" />
                                    </li>
                                    <li>
                                        <span class="amber-icon amber-icon--food" data-hover-text="Rationen">:</span>
                                        <xsl:value-of select="@food" />
                                    </li>
                                </ul>
                            </div>
                            <ul class="amber-character__combat-info">
                                <li class="amber-character__name amber-text amber-text--yellow">
                                    <xsl:value-of select="@name" />
                                </li>
                                <li>
                                    <span data-ch="3">lp:</span>
                                    <span data-ch="4">
                                        <xsl:value-of select="saa:class-instance/saa:hp/@current" />
                                    </span>
                                    <span>/</span>
                                    <span data-ch="4">
                                        <xsl:value-of select="saa:class-instance/saa:hp/@maximum" />
                                    </span>
                                    <span data-ch="5">tp:</span>
                                    <span data-ch="3">
                                        <xsl:value-of select="@training-points" />
                                    </span>
                                </li>
                                <li>
                                    <xsl:if test="$isMage">
                                        <span data-ch="3">sp:</span>
                                        <span data-ch="4">
                                            <xsl:value-of select="saa:class-instance/saa:sp/@current" />
                                        </span>
                                        <span>/</span>
                                        <span data-ch="4">
                                            <xsl:value-of select="saa:class-instance/saa:sp/@maximum" />
                                        </span>
                                        <span data-ch="5">slp:</span>
                                        <span data-ch="3">
                                            <xsl:value-of select="@spelllearn-points" />
                                        </span>
                                    </xsl:if>
                                </li>
                                <xsl:if test="saa:equipment">
                                    <li>
                                        <span class="amber-icon amber-icon--attack" data-hover-text="Schaden">:</span>
                                        <span data-ch="4">
                                            <xsl:choose>
                                                <xsl:when test="@attack > 0">
                                                    <xsl:text>+</xsl:text>
                                                    <xsl:value-of select="@attack" />
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:value-of select="@attack" />
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </span>
                                        <span data-ch="4" class="amber-icon amber-icon--magic-attack" data-hover-text="Magische Angriffskraft">:</span>
                                        <span>
                                            <xsl:value-of select="@magic-attack" />
                                        </span>
                                    </li>
                                    <li>
                                        <span class="amber-icon amber-icon--defense" data-hover-text="Schutz">:</span>
                                        <span data-ch="4">
                                            <xsl:choose>
                                                <xsl:when test="@defense > 0">
                                                    <xsl:text>+</xsl:text>
                                                    <xsl:value-of select="@defense" />
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:value-of select="@defense" />
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </span>
                                        <span data-ch="4" class="amber-icon amber-icon--magic-defense" data-hover-text="Magischer Rüstschutz">:</span>
                                        <span>
                                            <xsl:value-of select="@magic-defense" />
                                        </span>
                                    </li>
                                </xsl:if>
                            </ul>
                        </div>
                    </div>
                    <xsl:if test="saa:inventory">
                        <div class="amber-character__inventory">
                            <h3 class="amber-text amber-text--green">inventar</h3>
                            <xsl:apply-templates select="saa:inventory" mode="character" />
                        </div>
                    </xsl:if>
                </div>
            </div>
            <xsl:if test="saa:class-instance/saa:spellbook-instance/*">
                <div class="amber-character__spells">
                    <h3 class="amber-text amber-text--green">Zaubersprüche</h3>
                    <xsl:apply-templates select="saa:class-instance/saa:spellbook-instance[*]" mode="character" />
                </div>
            </xsl:if>
            <xsl:if test="saa:event">
                <div class="amber-character__dialog">
                    <h3 class="amber-text amber-text--green">dialogoptionen</h3>
                    <xsl:for-each select="saa:event">
                        <details>
                            <summary>
                                <xsl:for-each select="saa:trigger">
                                    <xsl:value-of select="@name" />
                                    <xsl:if test="@value">
                                        <xsl:text>: </xsl:text>
                                        <span class="amber-text amber-text--yellow">
                                            <xsl:value-of select="@value" />
                                        </span>
                                    </xsl:if>
                                </xsl:for-each>
                            </summary>
                            <xsl:for-each select="saa:text">
                                <div class="amber-textbox amber-text amber-text--silver">
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

    <xsl:template match="saa:monster" mode="character-picture">
        <amber-embed infoset="lib.monsters" type="monster">
            <amber-monster-id value="{@id}" />
        </amber-embed>
    </xsl:template>

    <xsl:template match="saa:pc | saa:npc" mode="character-picture">
        <amber-embed infoset="lib.portraits" type="portrait">
            <amber-portrait-id value="{@portrait-id}" />
        </amber-embed>
    </xsl:template>

    <xsl:template match="saa:spellbook-instance" mode="character">
        <ul>
            <xsl:for-each select="saa:spell-reference">
                <li class="amber-text amber-text--spellbook-{../@id}">
                    <xsl:value-of select="@name" />
                </li>
            </xsl:for-each>
        </ul>
    </xsl:template>

    <xsl:template match="saa:equipment" mode="character">
        <xsl:variable name="options" select="key('dictionary-option', 'equipment-slots')" />
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
    </xsl:template>

    <xsl:template match="saa:inventory" mode="character">
        <ul>
            <xsl:for-each select="saa:slot">
                <li>
                    <xsl:apply-templates select="." mode="itemlist-inline" />
                </li>
            </xsl:for-each>
        </ul>
    </xsl:template>

    <xsl:template match="saa:slot" mode="itemlist-inline">
        <amber-embed infoset="lib.items" type="item" id="{saa:item-instance/@item-id}" mode="popup">
            <amber-item-id value="{saa:item-instance/@item-id}" />
            <amber-item-amount value="{saa:item-instance/@item-amount}" />
            <xsl:if test="saa:item-instance/@is-identified">
                <amber-identified />
            </xsl:if>
            <xsl:if test="saa:item-instance/@is-broken">
                <amber-broken />
            </xsl:if>
            <amber-item-charge value="{saa:item-instance/@item-charge}" />
        </amber-embed>
        <xsl:if test="@name">
            <span class="name">
                <xsl:value-of select="@name" />
            </span>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
