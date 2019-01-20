<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ocpApimSubscriptionKey = '--Key Redacted--';
    $uriBase = 'https://uksouth.api.cognitive.microsoft.com/vision/v2.0/';

    $imageUrl = htmlspecialchars($_POST["url"]);

    require_once 'vendor/autoload.php';
    require_once 'vendor/pear/http_request2/HTTP/Request2.php';

    $request = new Http_Request2($uriBase . '/analyze');
    $url = $request->getUrl();

    $headers = array(
        'Content-Type' => 'application/json',
        'Ocp-Apim-Subscription-Key' => $ocpApimSubscriptionKey
    );
    $request->setHeader($headers);

    $parameters = array(
        'details' => 'Celebrities',
        'language' => 'en'
    );
    $url->setQueryVariables($parameters);

    $request->setMethod(HTTP_Request2::METHOD_POST);

    $body = json_encode(array('url' => $imageUrl));

    $request->setBody($body);

    $json = "";
    try
    {
        $response = $request->send();
        $json =
            json_encode(json_decode($response->getBody()), JSON_PRETTY_PRINT);
    }
    catch (HttpException $ex)
    {
        echo "<pre>" . $ex . "</pre>";
    }
}else{
    $json = "''";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Celebrity Finder</title>
</head>

<body>
<p id="celebrity"></p>
<h1> The Azure Celebrity Finder </h1>

<form method="post">
    Provide an image URL:<br>
    <input type="text" name="url"><br>
    <input type="submit" value="Submit">
</form>
</body>
</html>

<script type="text/javascript">
    var json_obj = <?php echo $json; ?>;
    if(json_obj.length !== 0){
        if(json_obj.categories.length !== 0){
            var categories = json_obj.categories;
            if(typeof categories[0].detail !== "undefined"){
                var detail = categories[0].detail;
                if(detail.celebrities.length !== 0){
                    document.getElementById("celebrity").innerHTML
                        = 'The celebrity in this image is '
                        + detail.celebrities[0].name + ', I am '
                        + detail.celebrities[0].confidence + '% sure.';
                }else{
                    document.getElementById("celebrity").innerHTML
                        = "I can't find a celebrity in this image";
                }
            }else{
                document.getElementById("celebrity").innerHTML
                    = "I can't find a celebrity in this image";
            }
        }else{
            document.getElementById("celebrity").innerHTML
                = "I can't find a celebrity in this image";
        }
    }
</script>
