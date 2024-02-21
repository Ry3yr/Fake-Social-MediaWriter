# Write the Social Media you like


## Love SocialMedia ? 
But are tired of ppl interacting with you ? 
Want that sweet private timeline on your site without interruption ?

## Here you go ! 
Supports: 
• Posting to specified user tl ( ?user=username) 
• Rendering tl in html via render.html ( ?user=username)
• Parsing and displaying pixiv, Youtube and links 
• displaying emoji from your desired location 
• crossposting via post2mtd.html 0% hassle

## Demo:
TL Output currently viewable [here](https://alceawis.de/about)

## Extra Info

If you want to embed "render.html" or "render_limited.html" anywhere, nomatter if iframe or doc.load, make sure to hardcode the pathe to the data_${user}.json !
Also your target url must contain ?user=useername for the specified user lest it won't load

The crossposter needs to be properly configured too incase you want to use it
(Meaning, fill out userid, apikey and instanceurl)
It is currently used with mas.to which is also where the emoji stem from

## I only have a static website ..

The render.html will still render the json fine, but you'd have to edit it by hand.
The postmtd will work on static websites too.
