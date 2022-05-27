let api_url = "https://api.unsplash.com/collections/84cUy-0KZSQ/photos?per_page=25&client_id=";
let api_key = "b_HZr630vSOOalwqLvfT30ZO1e-elwheolPi8izaCXI";
const photos = [];
var prelaodedImages = [];
document.addEventListener('DOMContentLoaded', () => {
    let headersList = {
        "Accept": "*/*",
        "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 12_3_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.88 Safari/537.36"
    }
    fetch(api_url + api_key, {
        method: "GET",
        headers: headersList
    }).then(function(response) {
        return response.text();
    }).then(function(data) {
        let content = "";
        JSON.parse(data).forEach(photo => {
            photos.push(photo.urls.regular)
            content += "url(" + photo.urls.regular + ") ";
        });

        preloadImages();

        let rnd_num = Math.floor(Math.random() * photos.length);
        ubg(photos[rnd_num]);
    })
});

function ubg(link) {
    document.body.style.backgroundImage = "url(" + link + ")";
    let rnd_min = rnd(5, 6);
    let rnd_sec = rnd(750, 1000);
    setTimeout(() => {
        let rnd_num = Math.floor(Math.random() * photos.length);
        ubg(photos[rnd_num]);
    }, rnd_min * rnd_sec);
}

function rnd(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function preloadImages() {
    for (i = 0; i < photos.length; i++) {
        prelaodedImages[i] = new Image();
        prelaodedImages[i].src = photos[i];
    }
}