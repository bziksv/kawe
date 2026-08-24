<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( !empty( $_GET["id"] ) )
    $id = (int)$_GET["id"];

if( !empty( $_GET["quantity"] ) )
    $quantity = (int)$_GET["quantity"];
else
    $quantity = 1;

if( !$id )
    die( 'Ошибка добавления товара в корзину!' );

CModule::IncludeModule( 'catalog' );
CModule::IncludeModule( 'sale' );
CModule::IncludeModule( 'iblock' );

function kaweNormalizeArticleValue($value)
{
    if (is_array($value))
        $value = reset($value);

    $value = trim((string)$value);

    if ($value === '' || $value === 'undefined' || $value === 'null')
        return '';

    return $value;
}

function kaweGetProductArticle($productId)
{
    $res = CIBlockElement::GetList(
        [],
        ["IBLOCK_ID" => IBLOCK_CATALOG, "ID" => $productId, "ACTIVE" => "Y"],
        false,
        false,
        ["ID", "PROPERTY_CML2_ARTICLE", "PROPERTY_ARTICLS"]
    );

    if (!$row = $res->Fetch())
        return '';

    if ($article = kaweNormalizeArticleValue($row['PROPERTY_ARTICLS_VALUE']))
        return $article;

    return kaweNormalizeArticleValue($row['PROPERTY_CML2_ARTICLE_VALUE']);
}

$FIELDS = [];
$PROPS = [];
$art = kaweNormalizeArticleValue($_GET["art"] ?? '');

if ($art === '')
    $art = kaweGetProductArticle($id);

if ($art !== '') {
    $arSelect = Array("ID", "IBLOCK_ID", "NAME","PROPERTY_*");
    $arFilter = Array("IBLOCK_ID" => IBLOCK_CATALOG, "ID" => $id, "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ob = $res->GetNextElement()){
        $arProps = $ob->GetProperties();
        $key_price = array_search($art, $arProps['PRICES']['DESCRIPTION']);
        if($key_price !== false && !empty($arProps['PRICES']['VALUE'][$key_price])){

            $arDiscounts = CCatalogDiscount::GetDiscount($id, IBLOCK_CATALOG);
            $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                $arProps['PRICES']['VALUE'][$key_price],
                "RUB",
                $arDiscounts
            );
            $arProps['PRICES']['VALUE'][$key_price] = ($discountPrice) ? $discountPrice : $arProps['PRICES']['VALUE'][$key_price];
            $FIELDS = ["PRICE" => $arProps['PRICES']['VALUE'][$key_price],"CUSTOM_PRICE" => "Y"];
        }
    }
    $PROPS[] = ["NAME" => "Артикул","CODE" => "CML2_ARTICLE","VALUE" => $art];
}

if( !empty( $_GET["color"] ) )
$PROPS[] = ["NAME" => "Вариант","CODE" => "CML2_OPTION","VALUE" => $_GET["color"]];

if(Add2BasketByProductID(
    $id,
    $quantity,
    $FIELDS,
    $PROPS
    )
){
    print 'Товар успешно добавлен в корзину';
}else{
    print 'Ошибка добавления товара в корзину';
}
?>