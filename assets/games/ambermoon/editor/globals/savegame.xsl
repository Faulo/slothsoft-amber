<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor" xmlns:sfx="http://schema.slothsoft.net/farah/xslt"
    xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">

    <xsl:import href="farah://slothsoft@farah/xsl/xslt" />

    <xsl:template name="savegame.amber.testing">
        <xsl:for-each select=".//*[@name = 'testing']">
            <div>
                <xsl:call-template name="savegame.table">
                    <xsl:with-param name="label" select="'Unbekannt'" />
                    <xsl:with-param name="items">
                        <xsl:apply-templates select="*" mode="item" />
                    </xsl:with-param>
                </xsl:call-template>
            </div>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="savegame.amber.events">
        <xsl:for-each select=".//*[@name = 'events']">
            <xsl:for-each select="sse:event-script | sse:binary">
                <div>
                    <h3 class="name">Events</h3>
                    <xsl:apply-templates select="." mode="form-content" />
                </div>
            </xsl:for-each>
            <xsl:for-each select="sse:instruction[@type = 'event-dictionary'][*]">
                <div class="events">
                    <!-- <xsl:call-template name="savegame.flex"> <xsl:with-param name="label" select="'Events'"/> <xsl:with-param name="class" select="'events'"/> <xsl:with-param name="items"> <xsl:for-each select="sse:instruction[@type = 'event']"> <div> <h3 class="name"><xsl:value-of select="@name"/></h3> 
                        <xsl:call-template name="savegame.table"> <xsl:with-param name="items"> <xsl:apply-templates select="sse:instruction[@type = 'event-step']/*" mode="item"/> </xsl:with-param> </xsl:call-template> </div> </xsl:for-each> </xsl:with-param> </xsl:call-template> -->
                    <h3 class="name">Events</h3>
                    <table>
                        <thead>
                            <tr>
                                <td class="yellow">#</td>
                                <td>type</td>
                                <td>subtype</td>
                                <td>payload</td>
                                <td>goto #</td>
                                <td>null</td>
                                <td>FFFF</td>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="*/*">
                                <xsl:if test="not(preceding-sibling::*)">
                                    <tr>
                                        <td colspan="4" class="green">
                                            <xsl:value-of select="../@name" />
                                        </td>
                                    </tr>
                                </xsl:if>
                                <tr>
                                    <td class="yellow">
                                        <xsl:value-of select="position() - 1" />
                                    </td>
                                    <td>
                                        <xsl:apply-templates select="*[@name='event-type']" mode="form-content" />
                                    </td>
                                    <td>
                                        <xsl:apply-templates select="*[@name='event-subtype']" mode="form-content" />
                                    </td>
                                    <td>
                                        <xsl:apply-templates select="*[@name='event-payload']" mode="form-content" />
                                    </td>
                                    <td>
                                        <xsl:apply-templates select="*[@name='event-goto']" mode="form-content" />
                                    </td>
                                    <td>
                                        <xsl:apply-templates select="*[@name='event-null'][@value != '0']" mode="form-content" />
                                    </td>
                                    <td>
                                        <xsl:apply-templates select="*[@name='event-FFFF'][@value != '255']" mode="form-content" />
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="savegame.amber.mobs">
        <xsl:for-each select=".//*[@name='mobs']">
            <xsl:call-template name="savegame.flex">
                <xsl:with-param name="label" select="'mobs'" />
                <xsl:with-param name="items">
                    <xsl:for-each select="*[*[1]/@value &gt; 0]">
                        <div>
                            <xsl:call-template name="savegame.table">
                                <xsl:with-param name="label" select="concat('mob #', position())" />
                                <xsl:with-param name="items">
                                    <xsl:apply-templates select="*" mode="item" />
                                </xsl:with-param>
                            </xsl:call-template>
                        </div>
                    </xsl:for-each>
                </xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="savegame.amber.fields">
        <xsl:variable name="width" select=".//*[@name='width']/@value" />
        <xsl:variable name="height" select=".//*[@name='height']/@value" />
        <xsl:variable name="tileset" select=".//*[@name='tileset-id']/@value" />
        <xsl:variable name="palette" select=".//*[@name='palette-id']/@value" />
        <xsl:variable name="map-type" select=".//*[@name='map-type']/@value" />
        <xsl:for-each select=".//*[@name='fields']">
            <xsl:variable name="fields" select="*" />
            <div>
                <h3 class="name">fields</h3>
                <div class="map">

                    <table data-palette="{$palette}">
                        <xsl:choose>
                            <xsl:when test="$map-type = 1">
                                <!--3D -->
                                <xsl:attribute name="data-tileset-lab"><xsl:value-of select="$tileset" /></xsl:attribute>
                            </xsl:when>
                            <xsl:when test="$map-type = 2">
                                <!--2D -->
                                <xsl:attribute name="data-tileset-icon"><xsl:value-of select="$tileset" /></xsl:attribute>
                            </xsl:when>
                        </xsl:choose>
                        <tbody>
                            <xsl:for-each select="sfx:range(1, $height)">
                                <xsl:variable name="y" select="position()" />
                                <tr>
                                    <xsl:for-each select="sfx:range(1, $width)">
                                        <xsl:variable name="x" select="position()" />
                                        <td title="{$x}|{$y}">
                                            <xsl:apply-templates select="$fields[($y - 1) * $width + $x]" mode="tile-picker" />
                                        </td>
                                    </xsl:for-each>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </div>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="savegame.amber.character-common">
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="'Allgemein'" />
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'character-type']" mode="item" />
                <xsl:for-each select=".//*[@name = 'portrait-id']">
                    <div>
                        <xsl:apply-templates select="." mode="form-name" />
                        <xsl:apply-templates select="." mode="portrait-picker" />
                    </div>
                </xsl:for-each>
                <xsl:apply-templates select=".//*[@name = 'name']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'gender']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'experience']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'level']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'attacks-per-round']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'hit-points']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'spell-points']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'training-points']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'spelllearn-points']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    <xsl:template name="savegame.amber.character-ailments">
        <xsl:apply-templates select=".//*[@name = 'ailments']" mode="item">
            <xsl:with-param name="class" select="'ailments'" />
        </xsl:apply-templates>
    </xsl:template>
    <xsl:template name="savegame.amber.character-combat">
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="'Monsterwerte'" />
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'combat-experience']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'combat-attack']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'combat-defense']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
        <xsl:apply-templates select=".//*[@name = 'monster-type']" mode="item" />
    </xsl:template>
    <xsl:template name="savegame.amber.character-gfx">
        <xsl:apply-templates select="." mode="monster-sprite-picker" />
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="'sprite data'" />
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'gfx-id']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'width']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'height']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
        <xsl:variable name="animationCycles" select=".//*[@name = 'cycle']" />
        <xsl:variable name="animationLengths" select=".//*[@name = 'length']" />
        <xsl:variable name="animationMirrors" select=".//*[@name = 'mirror']/*" />
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="'sprite animations'" />
            <xsl:with-param name="items">
                <xsl:for-each select="$animationCycles">
                    <xsl:variable name="pos" select="position()" />
                    <div>
                        <div class="name">
                            <xsl:call-template name="savegame.table">
                                <xsl:with-param name="label" select="../@name" />
                                <xsl:with-param name="items">
                                    <xsl:for-each select="$animationLengths[$pos]">
                                        <xsl:apply-templates select="." mode="item" />
                                    </xsl:for-each>
                                    <xsl:for-each select="$animationMirrors[$pos]">
                                        <div>
                                            <xsl:apply-templates select="." mode="form-attributes" />
                                            <span class="name">
                                                <xsl:value-of select="../@name" />
                                            </span>
                                            <xsl:apply-templates select="." mode="form-content" />
                                        </div>
                                    </xsl:for-each>
                                </xsl:with-param>
                            </xsl:call-template>
                        </div>
                        <div>
                            <xsl:apply-templates select="." mode="form-content" />
                        </div>
                    </div>
                </xsl:for-each>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    <xsl:template name="savegame.amber.character-race">
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="'Rasse'" />
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'race']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'age']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
        <xsl:for-each select=".//*[@name = 'attributes']">
            <xsl:call-template name="savegame.table">
                <xsl:with-param name="label" select="'Attribute'" />
                <xsl:with-param name="class" select="'attributes'" />
                <xsl:with-param name="items">
                    <xsl:apply-templates select="*" mode="item" />
                </xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
        <xsl:apply-templates select=".//*[@name = 'languages']" mode="item" />
    </xsl:template>
    <xsl:template name="savegame.amber.character-class">
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="'Klasse'" />
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'class']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'spellbooks']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
        <xsl:for-each select=".//*[@name = 'skills']">
            <xsl:call-template name="savegame.table">
                <xsl:with-param name="label" select="'Fähigkeiten'" />
                <xsl:with-param name="class" select="'skills'" />
                <xsl:with-param name="items">
                    <xsl:apply-templates select="*" mode="item" />
                </xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="label" select="''" />
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'apr-per-level']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'hp-per-level']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'sp-per-level']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'tp-per-level']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'slp-per-level']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    <xsl:template name="savegame.amber.character-equipment">
        <xsl:for-each select=".//*[@name = 'equipment']">
            <xsl:call-template name="savegame.flex">
                <xsl:with-param name="label" select="'Ausrüstung'" />
                <xsl:with-param name="class" select="'equipment'" />
                <xsl:with-param name="items">
                    <xsl:for-each select="*">
                        <div>
                            <xsl:apply-templates select="." mode="item-picker" />
                            <xsl:apply-templates select="." mode="form-name" />
                        </div>
                    </xsl:for-each>
                </xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
        <xsl:call-template name="savegame.table">
            <xsl:with-param name="items">
                <xsl:apply-templates select=".//*[@name = 'hand']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'finger']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'attack']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'defense']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'magic-attack']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'magic-defense']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'gold']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'food']" mode="item" />
                <xsl:apply-templates select=".//*[@name = 'weight']" mode="item" />
            </xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="savegame.button">
            <xsl:with-param name="label" select="'apply equipment'" />
            <xsl:with-param name="action" select="'savegameEditor.setEquipment(this);'" />
        </xsl:call-template>
    </xsl:template>
    <xsl:template name="savegame.amber.character-inventory">
        <xsl:for-each select=".//*[@name = 'inventory']">
            <xsl:call-template name="savegame.flex">
                <xsl:with-param name="label" select="'Inventar'" />
                <xsl:with-param name="class" select="'inventory'" />
                <xsl:with-param name="items">
                    <xsl:apply-templates select="*" mode="item-picker" />
                </xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="savegame.amber.character-spells">
        <xsl:for-each select=".//*[@name = 'spells']">
            <xsl:call-template name="savegame.flex">
                <xsl:with-param name="label" select="'Zaubersprüche'" />
                <xsl:with-param name="class" select="'spells'" />
                <xsl:with-param name="items">
                    <xsl:apply-templates select="*" mode="item" />
                </xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="savegame.amber.shop">
        <xsl:call-template name="savegame.flex">
            <xsl:with-param name="items">
                <xsl:for-each select="*[@name = 'inventory']">
                    <div>
                        <xsl:call-template name="savegame.flex">
                            <xsl:with-param name="label" select="'Inventar'" />
                            <xsl:with-param name="class" select="'shop'" />
                            <xsl:with-param name="items">
                                <xsl:apply-templates select="*/*" mode="item-picker" />
                            </xsl:with-param>
                        </xsl:call-template>
                    </div>
                </xsl:for-each>
                <xsl:for-each select="*[@name = 'valuables']">
                    <div>
                        <xsl:call-template name="savegame.table">
                            <xsl:with-param name="label" select="'Wertvolles'" />
                            <xsl:with-param name="items">
                                <xsl:apply-templates select="*" mode="item" />
                            </xsl:with-param>
                        </xsl:call-template>
                    </div>
                </xsl:for-each>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>



    <xsl:template match="node()" mode="item">
        <pre class="errorMessage">
            Unbekanntes form Element:
            <xsl:value-of select="name()" />
        </pre>
    </xsl:template>

    <xsl:template match="*" mode="item">
        <xsl:param name="class" select="''" />
        <div>
            <xsl:apply-templates select="." mode="form-attributes">
                <xsl:with-param name="class" select="$class" />
            </xsl:apply-templates>
            <xsl:apply-templates select="." mode="form-name" />
            <xsl:apply-templates select="." mode="form-content" />
        </div>
    </xsl:template>

    <xsl:template match="sse:instruction[@type = 'bit-field']" mode="item">
        <xsl:param name="class" select="''" />
        <div>
            <xsl:apply-templates select="." mode="form-attributes">
                <xsl:with-param name="class" select="$class" />
            </xsl:apply-templates>
            <xsl:apply-templates select="." mode="form-name" />
            <xsl:apply-templates select="." mode="form-content" />
        </div>
    </xsl:template>

    <xsl:template match="sse:instruction[@type = 'string-dictionary']" mode="item">
        <xsl:param name="class" select="''" />
        <div>
            <xsl:apply-templates select="." mode="form-attributes">
                <xsl:with-param name="class" select="$class" />
            </xsl:apply-templates>
            <xsl:apply-templates select="." mode="form-name" />
            <table>
                <tbody>
                    <xsl:for-each select="*">
                        <tr>
                            <td class="yellow">
                                <xsl:value-of select="position() - 1" />
                            </td>
                            <td>
                                <p class="gray smaller pre">
                                    <xsl:value-of select="@value" />
                                </p>
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </div>
    </xsl:template>

    <xsl:template match="sse:string | sse:integer | sse:signed-integer | sse:select | sse:binary | sse:event-script" mode="item">
        <xsl:param name="class" select="''" />
        <label>
            <xsl:apply-templates select="." mode="form-attributes">
                <xsl:with-param name="class" select="$class" />
            </xsl:apply-templates>
            <xsl:apply-templates select="." mode="form-name" />
            <xsl:apply-templates select="." mode="form-content" />
        </label>
    </xsl:template>

    <xsl:template match="sse:bit" mode="item">
        <xsl:param name="class" select="''" />
        <label>
            <xsl:apply-templates select="." mode="form-attributes">
                <xsl:with-param name="class" select="$class" />
            </xsl:apply-templates>
            <xsl:apply-templates select="." mode="form-content" />
            <xsl:apply-templates select="." mode="form-name" />
        </label>
    </xsl:template>


    <!-- form-content -->
    <xsl:template match="*" mode="form-content">
        <pre class="errorMessage">
            Unbekanntes form-content Element:
            <xsl:value-of select="name()" />
        </pre>
    </xsl:template>

    <!-- form-content instructions -->
    <xsl:template match="sse:group | sse:instruction" mode="form-content">
        <xsl:choose>
            <xsl:when test="@dictionary-ref">
                <xsl:variable name="options" select="key('dictionary-option', @dictionary-ref)" />
                <xsl:for-each select="*">
                    <xsl:variable name="key" select="position() - 1" />
                    <xsl:apply-templates select="." mode="item">
                        <xsl:with-param name="name" select="$options[@key = $key]/@val" />
                    </xsl:apply-templates>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="*" mode="item" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="sse:instruction[@type = 'bit-field']" mode="form-content">
        <ul>
            <xsl:for-each select="sse:bit">
                <li>
                    <xsl:apply-templates select="." mode="item" />
                </li>
            </xsl:for-each>
        </ul>
    </xsl:template>
    <xsl:template match="sse:instruction[@type = 'event']" mode="form-content">
        <xsl:apply-templates select="*" mode="form-content" />
    </xsl:template>
    <xsl:template match="sse:group[*[@name = 'current']]" mode="form-content">
        <xsl:apply-templates select="*[@name = 'current']" mode="form-content" />
        <xsl:text>/</xsl:text>
        <xsl:apply-templates select="*[@name = 'maximum']" mode="form-content" />
    </xsl:template>
    <xsl:template match="sse:group[*[@name = 'current'] and *[@name = 'current-mod']]" mode="form-content">
        <xsl:apply-templates select="*[@name = 'current']" mode="form-content" />
        <xsl:text>+</xsl:text>
        <xsl:apply-templates select="*[@name = 'current-mod']" mode="form-content" />
        <xsl:text>/</xsl:text>
        <xsl:apply-templates select="*[@name = 'maximum']" mode="form-content" />
    </xsl:template>
    <xsl:template match="sse:group[*[@name = 'current'] and *[@name = 'maximum-mod']]" mode="form-content">
        <xsl:apply-templates select="*[@name = 'current']" mode="form-content" />
        <xsl:text>/</xsl:text>
        <xsl:apply-templates select="*[@name = 'maximum']" mode="form-content" />
        <xsl:text>+</xsl:text>
        <xsl:apply-templates select="*[@name = 'maximum-mod']" mode="form-content" />
    </xsl:template>
    <xsl:template match="sse:group[*[@name = 'current'] and *[@name = 'current-mod'] and *[@name = 'maximum-mod']]" mode="form-content">
        <xsl:apply-templates select="*[@name = 'current']" mode="form-content" />
        <xsl:text>+</xsl:text>
        <xsl:apply-templates select="*[@name = 'current-mod']" mode="form-content" />
        <xsl:text>/</xsl:text>
        <xsl:apply-templates select="*[@name = 'maximum']" mode="form-content" />
        <xsl:text>+</xsl:text>
        <xsl:apply-templates select="*[@name = 'maximum-mod']" mode="form-content" />
    </xsl:template>
    <xsl:template match="sse:group[*[@name = 'source'] and *[@name = 'target']]" mode="form-content">
        <xsl:apply-templates select="*[@name = 'target']" mode="form-content" />
        <xsl:text>/</xsl:text>
        <xsl:apply-templates select="*[@name = 'source']" mode="form-content" />
    </xsl:template>

    <xsl:template match="*" mode="portrait-picker">
        <amber-picker infoset="lib.portraits" type="portrait" class="portrait-picker" contextmenu="amber-picker-portrait" role="button" tabindex="0" onclick="savegameEditor.openPopup(arguments[0])">
            <xsl:apply-templates select="." mode="form-picker" />
        </amber-picker>
    </xsl:template>
    <xsl:template match="*" mode="item-picker">
        <xsl:variable name="itemId" select=".//*[@name = 'item-id']/@value" />
        <!--<xsl:variable name="item" select="key('item', $itemId)" /> data-hover-text="{$item/@name}" -->
        <amber-picker infoset="lib.items" type="item" class="item-picker" contextmenu="amber-picker-item" role="button" tabindex="0" onclick="savegameEditor.openPopup(arguments[0])">
            <xsl:if test="../@name = 'equipment'">
                <xsl:attribute name="data-picker-filter-amber-item-id"><xsl:value-of select="saa:getName()" /></xsl:attribute>
            </xsl:if>
            <xsl:apply-templates select=".//*[@name = 'item-id']" mode="form-picker" />
            <xsl:apply-templates select=".//*[@name = 'item-amount']" mode="form-picker" />
            <xsl:apply-templates select=".//*[@name = 'broken']" mode="form-picker" />
            <xsl:apply-templates select=".//*[@name = 'identified']" mode="form-picker" />
            <xsl:apply-templates select=".//*[@name = 'item-charge']" mode="form-picker" />
        </amber-picker>
    </xsl:template>
    <xsl:template match="*" mode="tile-picker">
        <amber-picker infoset="lib.tileset.icons" type="tileset-icon" class="tile-picker" contextmenu="amber-picker-tileset-icon" role="button" tabindex="0" onclick="savegameEditor.openPopup(arguments[0])">
            <xsl:apply-templates select="*[1]" mode="form-picker">
                <xsl:with-param name="name" select="'tile-id'" />
            </xsl:apply-templates>
            <xsl:apply-templates select="*[2]" mode="form-picker">
                <xsl:with-param name="name" select="'tile-id'" />
            </xsl:apply-templates>
            <xsl:apply-templates select="*[3]" mode="form-picker">
                <xsl:with-param name="name" select="'event-id'" />
            </xsl:apply-templates>
        </amber-picker>
    </xsl:template>
    <xsl:template match="*" mode="monster-sprite-picker">
        <amber-picker infoset="lib.monsters" type="monster-sprite" class="monster-sprite-picker" contextmenu="amber-picker-monster-sprite" role="button" tabindex="0" onclick="savegameEditor.openPopup(arguments[0])">
            <xsl:apply-templates select=".//*[@name = 'gfx-id']" mode="form-picker" />
        </amber-picker>
    </xsl:template>

    <!-- form-content values -->
    <xsl:template match="sse:integer | sse:signed-integer" mode="form-content">
        <input name="save[data][{@value-id}]" value="{@value}">
            <xsl:if test="string-length(@name)">
                <xsl:attribute name="data-name"><xsl:value-of select="@name" /></xsl:attribute>
            </xsl:if>
            <xsl:if test="@readonly">
                <xsl:attribute name="readonly">readonly</xsl:attribute>
            </xsl:if>
            <xsl:if test="@disabled">
                <xsl:attribute name="disabled">disabled</xsl:attribute>
            </xsl:if>
            <xsl:attribute name="type">number</xsl:attribute>
            <xsl:attribute name="step">1</xsl:attribute>
            <xsl:attribute name="min"><xsl:value-of select="@min" /></xsl:attribute>
            <xsl:attribute name="max"><xsl:value-of select="@max" /></xsl:attribute>
            <xsl:attribute name="size"><xsl:value-of select="@size" /></xsl:attribute>
        </input>
    </xsl:template>

    <xsl:template match="sse:string" mode="form-content">
        <input name="save[data][{@value-id}]" value="{@value}">
            <xsl:if test="string-length(@name)">
                <xsl:attribute name="data-name"><xsl:value-of select="@name" /></xsl:attribute>
            </xsl:if>
            <xsl:if test="@readonly">
                <xsl:attribute name="readonly">readonly</xsl:attribute>
            </xsl:if>
            <xsl:if test="@disabled">
                <xsl:attribute name="disabled">disabled</xsl:attribute>
            </xsl:if>
            <xsl:attribute name="type">text</xsl:attribute>
            <xsl:attribute name="maxlength"><xsl:value-of select="@size" /></xsl:attribute>
            <!--<xsl:attribute name="ng-model"><xsl:value-of select="@name"/></xsl:attribute> -->
        </input>
    </xsl:template>

    <xsl:template match="sse:binary" mode="form-content">
        <xsl:variable name="cols" select="24" />
        <xsl:variable name="rows" select="ceiling(string-length(@value) div $cols)" />
        <textarea name="save[data][{@value-id}]" rows="{$rows}" cols="{$cols}" data-type="{local-name()}">
            <xsl:if test="@readonly">
                <xsl:attribute name="readonly">readonly</xsl:attribute>
            </xsl:if>
            <xsl:if test="@disabled">
                <xsl:attribute name="disabled">disabled</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@value" />
        </textarea>
    </xsl:template>

    <xsl:template match="sse:event-script" mode="form-content">
        <textarea name="save[data][{@value-id}]" rows="20" cols="40" data-type="event-script">
            <xsl:if test="@readonly">
                <xsl:attribute name="readonly">readonly</xsl:attribute>
            </xsl:if>
            <xsl:if test="@disabled">
                <xsl:attribute name="disabled">disabled</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@value" />
        </textarea>
    </xsl:template>

    <xsl:template match="sse:bit" mode="form-content">
        <input type="checkbox" name="save[data][{@value-id}_checkbox]">
            <xsl:if test="@value &gt; 0">
                <xsl:attribute name="checked">checked</xsl:attribute>
            </xsl:if>
        </input>
        <input type="hidden" name="save[data][{@value-id}]" value="_checkbox" />
    </xsl:template>

    <xsl:template match="sse:select" mode="form-content">
        <xsl:variable name="node" select="." />
        <xsl:variable name="options" select="key('dictionary-option', @dictionary-ref)" />
        <select name="save[data][{@value-id}]">
            <xsl:if test="string-length(@name)">
                <xsl:attribute name="data-name"><xsl:value-of select="@name" /></xsl:attribute>
            </xsl:if>
            <xsl:if test="@disabled">
                <xsl:attribute name="disabled">disabled</xsl:attribute>
            </xsl:if>
            <xsl:for-each select="$options">
                <option value="{@key}">
                    <xsl:if test="@key = $node/@value">
                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="@val" />
                </option>
            </xsl:for-each>
            <xsl:if test="not($node/@value = $options/@key)">
                <option value="{$node/@value}" selected="selected">
                    <xsl:value-of select="concat('??? (', $node/@value, ')')" />
                </option>
            </xsl:if>
        </select>
    </xsl:template>



    <!-- form-attributes -->
    <xsl:template match="*" mode="form-attributes">
        <xsl:param name="class" select="''" />
        <xsl:if test="string-length($class)">
            <xsl:attribute name="class"><xsl:value-of select="$class" /></xsl:attribute>
        </xsl:if>
        <xsl:if test="string-length(@type)">
            <xsl:attribute name="data-type"><xsl:value-of select="@type" /></xsl:attribute>
        </xsl:if>
        <!-- <xsl:if test="string-length(@template)"> <xsl:attribute name="data-template"><xsl:value-of select="@template" /></xsl:attribute> </xsl:if> <xsl:if test="string-length(@dict)"> <xsl:attribute name="data-dictionary"><xsl:value-of select="@dict" /></xsl:attribute> </xsl:if> <xsl:if test="string-length(@instruction)"> 
            <xsl:attribute name="data-instruction"><xsl:value-of select="@instruction" /></xsl:attribute> </xsl:if> <xsl:if test="string-length(@name)"> <xsl:attribute name="data-name"><xsl:value-of select="@name" /></xsl:attribute> </xsl:if> -->
    </xsl:template>



    <!-- form-name -->
    <xsl:template match="*" mode="form-name">
        <xsl:if test="string-length(@name)">
            <span class="name">
                <xsl:if test="string-length(@title)">
                    <xsl:attribute name="data-hover-text"><xsl:value-of select="@title" /></xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@name" />
            </span>
        </xsl:if>
        <xsl:if test="../@dictionary-ref">
            <xsl:variable name="options" select="key('dictionary-option', ../@dictionary-ref)" />
            <xsl:variable name="key" select="count(preceding-sibling::*)" />
            <xsl:for-each select="$options[@key = $key]">
                <xsl:choose>
                    <xsl:when test="@description">
                        <abbr class="name" title="{@description}">
                            <xsl:value-of select="@val" />
                        </abbr>
                    </xsl:when>
                    <xsl:otherwise>
                        <span class="name">
                            <xsl:value-of select="@val" />
                        </span>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>
    <xsl:template match="sse:instruction[@type = 'bit-field']" mode="form-name">
        <xsl:copy-of select="." />
        <xsl:if test="string-length(@name)">
            <h3 class="name">
                <xsl:if test="string-length(@title)">
                    <xsl:attribute name="data-hover-text"><xsl:value-of select="@title" /></xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@name" />
            </h3>
        </xsl:if>
        <xsl:if test="../@dictionary-ref">
            <xsl:variable name="options" select="key('dictionary-option', ../@dictionary-ref)" />
            <xsl:variable name="key" select="count(preceding-sibling::*)" />
            <xsl:for-each select="$options[@key = $key]">
                <xsl:choose>
                    <xsl:when test="@description">
                        <h3>
                            <abbr class="name" title="{@description}">
                                <xsl:value-of select="@val" />
                            </abbr>
                        </h3>
                    </xsl:when>
                    <xsl:otherwise>
                        <h3 class="name">
                            <xsl:value-of select="@val" />
                        </h3>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>





    <!-- form-picker -->
    <xsl:template match="sse:group" mode="form-picker">
        <xsl:apply-templates select="*" mode="form-picker" />
    </xsl:template>
    <xsl:template match="sse:string | sse:integer | sse:signed-integer | sse:bit | sse:select" mode="form-picker">
        <xsl:param name="name" select="@name" />
        <xsl:element name="{concat('amber-', $name)}"><!-- namespace="http://schema.slothsoft.net/amber/xhtml" -->
            <xsl:attribute name="value"><xsl:value-of select="@value" /></xsl:attribute>
            <xsl:apply-templates select="." mode="form-hidden" />
        </xsl:element>
    </xsl:template>




    <!-- form-hidden -->
    <xsl:template match="*" mode="form-hidden">
        <xsl:if test="string-length(@value-id)">
            <input type="hidden" name="save[data][{@value-id}]" value="{@value}" />
        </xsl:if>
    </xsl:template>
    <xsl:template match="sse:bit" mode="form-hidden">
        <xsl:if test="string-length(@value-id)">
            <input type="checkbox" name="save[data][{@value-id}_checkbox]" hidden="hidden">
                <xsl:if test="@value &gt; 0">
                    <xsl:attribute name="checked">checked</xsl:attribute>
                </xsl:if>
            </input>
            <input type="hidden" name="save[data][{@value-id}]" value="_checkbox" />
        </xsl:if>
    </xsl:template>







    <!-- savegame templates -->
    <func:function name="saa:optionsForFiles">
        <xsl:param name="files" />
        <xsl:param name="dictionary-ref" />

        <func:result>
            <xsl:for-each select="$files">
                <token>
                    <xsl:value-of select="concat('[', @file-name, '] ')" />
                    <xsl:value-of select="saa:getDictionaryOption($dictionary-ref, number(@file-name))/@val" />
                </token>
            </xsl:for-each>
        </func:result>
    </func:function>
    <func:function name="saa:optionsForPositions">
        <xsl:param name="positions" />
        <xsl:param name="dictionary-ref" />

        <func:result>
            <xsl:for-each select="$positions">
                <xsl:variable name="pos" select="position()" />
                <token>
                    <xsl:value-of select="saa:getDictionaryOption($dictionary-ref, $pos)/@val" />
                </token>
            </xsl:for-each>
        </func:result>
    </func:function>

    <xsl:template name="savegame.tabs">
        <xsl:param name="label" select="''" />
        <xsl:param name="class" select="''" />
        <xsl:param name="list" select="/.." />
        <xsl:param name="optionTokens" select="/.." />
        <xsl:param name="options" select="exsl:node-set($optionTokens)/*" />

        <div data-template="tabs">
            <xsl:if test="string-length($class)">
                <xsl:attribute name="class"><xsl:value-of select="$class" /></xsl:attribute>
            </xsl:if>
            <label>
                <span class="name">
                    <xsl:value-of select="$label" />
                </span>
                <select class="command" disabled="disabled">
                    <xsl:for-each select="$options">
                        <option value="{position() - 1}">
                            <xsl:if test="count(ancestor::sse:file/../sse:file) &gt; 1 and ancestor::sse:file/@file-name != current()">
                                <xsl:value-of select="concat('[', ancestor::sse:file/@file-name, '] ')" />
                            </xsl:if>
                            <xsl:value-of select="." />
                        </option>
                    </xsl:for-each>
                </select>
            </label>
            <ul>
                <xsl:copy-of select="$list" />
            </ul>
        </div>
    </xsl:template>

    <xsl:template name="savegame.table">
        <xsl:param name="label" select="''" />
        <xsl:param name="class" select="''" />
        <xsl:param name="items" select="/.." />

        <xsl:variable name="rows" select="exsl:node-set($items)/*" />
        <xsl:if test="$rows">
            <xsl:if test="string-length($label)">
                <h3 class="name">
                    <xsl:value-of select="$label" />
                </h3>
            </xsl:if>
            <table>
                <xsl:if test="string-length($class)">
                    <xsl:attribute name="class"><xsl:value-of select="$class" /></xsl:attribute>
                </xsl:if>
                <tbody>
                    <xsl:for-each select="$rows">
                        <tr>
                            <xsl:copy-of select="@*" />
                            <th>
                                <xsl:copy-of select="*[@class='name']" />
                            </th>
                            <td>
                                <xsl:copy-of select="node()[not(@class='name')]" />
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </xsl:if>
    </xsl:template>

    <xsl:template name="savegame.flex">
        <xsl:param name="label" select="''" />
        <xsl:param name="class" select="''" />
        <xsl:param name="items" select="/.." />

        <div data-template="flex">
            <xsl:if test="string-length($class)">
                <xsl:attribute name="class"><xsl:value-of select="$class" /></xsl:attribute>
            </xsl:if>
            <xsl:if test="string-length($label)">
                <h3 class="name">
                    <xsl:value-of select="$label" />
                </h3>
            </xsl:if>
            <ul>
                <xsl:for-each select="exsl:node-set($items)/*">
                    <li>
                        <xsl:copy-of select="." />
                    </li>
                </xsl:for-each>
            </ul>
        </div>
    </xsl:template>

    <xsl:template name="savegame.button">
        <xsl:param name="label" select="''" />
        <xsl:param name="action" select="''" />

        <button type="button" onclick="{$action}">
            <xsl:value-of select="$label" />
        </button>
    </xsl:template>
</xsl:stylesheet>