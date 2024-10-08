<xsl:stylesheet version="1.0"
	xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">
	
	<xsl:variable name="static" select="document('')/*/saa:amberdata"/>
	
	<amberdata version="0.1">
		<class-list>
			<class id="0" name="Abenteurer" />
			<class id="1" name="Krieger" />
			<class id="2" name="Paladin" />
			<class id="3" name="Dieb" />
			<class id="4" name="Ranger" />
			<class id="5" name="Heiler" />
			<class id="6" name="Alchemist" />
			<class id="7" name="Mystiker" />
			<class id="8" name="Magier" />
			<class id="9" name="Katze" />
			<class id="10" name="(leer)" />
		</class-list>
		<portrait-list>
			<portrait-category name="Menschen und Halb-Elfen ♂">
				<portrait id="1" name="A" />
				<portrait id="2" name="B" />
				<portrait id="3" name="C" />
				<portrait id="4" name="D" />
				<portrait id="5" name="E" />
				<portrait id="6" name="F" />
				<portrait id="7" name="G" />
				<portrait id="8" name="H" />
				<portrait id="9" name="I" />
				<portrait id="10" name="J" />
				<portrait id="11" name="K" />
				<portrait id="12" name="L" />
				<portrait id="13" name="M" />
				<portrait id="14" name="N" />
				<portrait id="15" name="O" />
				<portrait id="16" name="P" />
				<portrait id="17" name="Q" />
				<portrait id="18" name="R" />
				<portrait id="19" name="S" />
				<portrait id="20" name="T" />
				<portrait id="21" name="U" />
				<portrait id="22" name="V" />
				<portrait id="23" name="W" />
				<portrait id="24" name="X" />
				<portrait id="25" name="Y" />
				<portrait id="26" name="Z" />
				<portrait id="27" name="$" />
				<portrait id="102" name="€" />
			</portrait-category>
			<portrait-category name="Menschen und Halb-Elfen ♀">
				<portrait id="28" name="A" />
				<portrait id="29" name="B" />
				<portrait id="30" name="C" />
				<portrait id="31" name="D" />
				<portrait id="32" name="E" />
				<portrait id="33" name="F" />
				<portrait id="34" name="G" />
				<portrait id="35" name="H" />
				<portrait id="36" name="I" />
				<portrait id="37" name="J" />
				<portrait id="38" name="K" />
				<portrait id="39" name="L" />
				<portrait id="40" name="M" />
				<portrait id="41" name="N" />
				<portrait id="42" name="O" />
				<portrait id="43" name="P" />
				<portrait id="44" name="Q" />
				<portrait id="45" name="R" />
				<portrait id="46" name="S" />
				<portrait id="47" name="T" />
				<portrait id="48" name="U" />
				<portrait id="49" name="V" />
				<portrait id="50" name="W" />
				<portrait id="51" name="X" />
				<portrait id="52" name="Y" />
				<portrait id="53" name="Z" />
			</portrait-category>
			<portrait-category name="Elfen ♂">
				<portrait id="61" name="A" />
				<portrait id="62" name="B" />
				<portrait id="63" name="C" />
				<portrait id="64" name="D" />
				<portrait id="65" name="E" />
				<portrait id="66" name="F" />
				<portrait id="67" name="G" />
				<portrait id="68" name="H" />
			</portrait-category>
			<portrait-category name="Elfen ♀">
				<portrait id="69" name="A" />
				<portrait id="70" name="B" />
				<portrait id="71" name="C" />
				<portrait id="72" name="D" />
				<portrait id="73" name="E" />
				<portrait id="74" name="F" />
				<portrait id="75" name="G" />
				<portrait id="76" name="H" />
				<portrait id="77" name="I" />
				<portrait id="78" name="J" />
			</portrait-category>
			<portrait-category name="Zwerge und Gnome ♂">
				<portrait id="54" name="A" />
				<portrait id="55" name="B" />
				<portrait id="56" name="C" />
				<portrait id="57" name="D" />
				<portrait id="58" name="E" />
				<portrait id="59" name="F" />
				<portrait id="60" name="G" />
				<portrait id="90" name="H" />
				<portrait id="91" name="I" />
				<portrait id="92" name="J" />
				<portrait id="93" name="K" />
				<portrait id="94" name="L" />
				<portrait id="95" name="M" />
				<portrait id="96" name="N" />
				<portrait id="97" name="O" />
				<portrait id="98" name="P" />
			</portrait-category>
			<portrait-category name="Zwerge und Gnome ♀">
				<portrait id="99" name="A" />
				<portrait id="100" name="B" />
				<portrait id="101" name="C" />
			</portrait-category>
			<portrait-category name="Sylphen">
				<portrait id="79" name="A" />
				<portrait id="80" name="B" />
			</portrait-category>
			<portrait-category name="Moraner">
				<portrait id="85" name="A" />
				<portrait id="86" name="B" />
				<portrait id="87" name="C" />
				<portrait id="88" name="D" />
				<portrait id="89" name="E" />
			</portrait-category>
			<portrait-category name="Tiere">
				<portrait id="81" name="A" />
				<portrait id="82" name="B" />
				<portrait id="83" name="C" />
				<portrait id="84" name="D" />
			</portrait-category>
		</portrait-list>
	</amberdata>
</xsl:stylesheet>