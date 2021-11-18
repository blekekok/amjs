var currentURL = new URLSearchParams(window.location.search);

if (currentURL.has('thread')) {
    let threadId = currentURL.get('thread')
    
    setActiveThread(threadId, true);
} else {
    setGroups();
}


function setGroups(active=-1, categoryid=-1) {
    let groups = getGroups();

    if (groups && groups.length) {
        let groupList = $('#group-list');
        groupList.empty();

        groups.forEach(group => {
            let listItem = document.createElement('li');
            listItem.innerHTML = `<span>${group.displayname}</span>`;

            if (group.id == active || active == -1 && group.id == groups[0].id) listItem.classList.add('active');
            
            listItem.onclick = () => {
                $('#group-list li').removeClass('active');
                listItem.classList.add('active');

                setCategories(group.id);
            }
            
            groupList.append(listItem);
        });
        
        if (active == -1) {
            setCategories(groups[0].id);
        } else {
            setCategories(active, categoryid);
        }
    }
}

function setCategories(groupid=1, active=-1) {
    let categories = getCategories(groupid);

    let categoryList = $('#category-list');
    categoryList.empty();
    $('#thread-list').empty();

    if (categories && categories.length) {
        categories.forEach(category => {
            let listItem = document.createElement('li');
            listItem.innerHTML = `<span>${category.displayname}</span>`;

            if (category.id == active || (active == -1 && category.id == categories[0].id)) listItem.classList.add('active');

            listItem.onclick = () => {
                $('#category-list li').removeClass('active');
                listItem.classList.add('active');
                setThreadTitles(category.id);
            }

            categoryList.append(listItem);
        });

        if (active == -1) {
            setThreadTitles(categories[0].id);
        } else {
            setThreadTitles(active);
        }
    }
}

function setThreadTitles(categoryid=2) {
    let threads = getThreadTitles(categoryid);
    let threadList = $('#thread-list');
    threadList.empty();

    if (threads) {

        threads.forEach(thread => {
            let listItem = document.createElement('li');

            // Badge
            let badgeElem = document.createElement('span');
            badgeElem.classList.add('badge');
            if (thread.hot) 
                badgeElem.innerHTML = '[HOT]';

            // Title
            let titleElem = document.createElement('span');
            titleElem.classList.add('title');
            titleElem.innerHTML = thread.title;

            titleElem.onclick = () => {

                setActiveThread(thread.id);

            }

            // Meta Div
            let metaDiv = document.createElement('div');
            metaDiv.classList.add('meta');

            // Sub Meta Div
            let subMetaDiv = document.createElement('div');
            subMetaDiv.classList.add('sub-meta');

            // Author
            let authorElem = document.createElement('span');
            authorElem.classList.add('author');
            authorElem.innerHTML = `by ${thread.author}`;

            // View Counter
            let viewsDiv = document.createElement('div');
            viewsDiv.innerHTML = `
                <img src="src/res/eye.svg" alt="">
                <span>${convertNumberToShortened(thread.views)}</span>
            `;
            subMetaDiv.append(authorElem, viewsDiv);

            // Total Post Counter
            let postsDiv = document.createElement('div');
            postsDiv.classList.add('posts');
            postsDiv.innerHTML = `
                <img src="src/res/comment.svg" alt="">
                <span>${convertNumberToShortened(thread.posts)}</span>
            `;

            // Time
            let timeElem = document.createElement('span');
            timeElem.classList.add('time');
            timeElem.innerHTML = formatTimeAsText(thread.age);
            metaDiv.append(subMetaDiv, postsDiv, timeElem);
            listItem.append(badgeElem, titleElem, metaDiv);
            threadList.append(listItem);
        });
    }
}

function setActiveThread(threadid, fromLink=false) {
    let result = getThreadContent(threadid);

    if (result) {
        if (fromLink) {
            setGroups(result.groupid, result.categoryid);
        }

        $('#bottom-seperator h1').html(`Thread in : ${result.displayname}`);
        $('#bottom-seperator h2').remove();

        $('#content-header').append(
            `
                <div id="thread-header">
                    <h1>${result.title}</h1>
                    <span>Posted on ${result.creation_date} by ${result.author}</span>
                    <div>
                        <img src="src/res/history.svg" alt="">
                        <span>${formatTimeAsText(result.lastactivity)}.</span>
                    </div>
                </div>
                <div class="seperator">
                    <div class="line"></div>
                </div>
            `
        );

        console.log(result);
    }
}

$('#thread-content').append(buildThreadPost());

function buildThreadPost() {

    let postWrapper = $('<div/>', {'class': 'post-wrapper'});

    return postWrapper;

}

function getGroups() {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-groups'
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData;
}

function getCategories(groupid=1) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-categories',
            groupid: groupid
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData;
}

function getThreadTitles(categoryid=1) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-thread-titles',
            categoryid: categoryid
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData;
}

function getThreadContent(threadid) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-thread-content',
            threadid: threadid
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            history.replaceState('', '', `/index.php?thread=${threadid}`);
            return responseData.content;

        case 500 && 404:
            history.replaceState('', '', '/');
            setGroups();
            break;

        case 401:
            window.location.replace('/login.php');
            break;
    }

    return null;
}

function formatTimeAsText(time = 0, short=true) {

    // Years
    if (time >= 31536000) {
        time = (time / 31536000) | 0;
        if (time == 1)
            return '1 year ago';

        return `${time} years`;
    }

    // Seconds
    if (time < 60)
        return 'Moments ago';
        
    // Minutes
    time =  (time / 60) | 0;
    if (time < 60)
        if (short) {
            return `${time}m ago`;
        } else {
            if (time == 1)
                return `${time} minute ago`;
            
            return `${time} minutes ago`;
        }

    // Hours
    time =  (time / 60) | 0;
    if (time < 24) {
        if (time == 1)
            return '1 hour ago';

        return `${time} hours ago`;
    }

    // Days
    time =  (time / 24) | 0;
    if (time < 7) {
        if (time == 1)
            return '1 day ago';

        return `${time} days ago`;
    }

    // Weeks
    time =  (time / 7) | 0;
    if (time < 4) {
        if (time == 1)
            return '1 week ago';

        return `${time} weeks ago`;
    }

    // Months
    time = (time / 4) | 0;
    if (time < 12) {
        if (time == 1)
            return '1 month ago';

        return `${time} months ago`;
    }

    return '1 year ago';
}

function convertNumberToShortened(num) {

    if (num >= 1000000000)
        return `${(num / 1000000000).toFixed(1)}B`;

    if (num >= 1000000)
        return `${(num / 1000000).toFixed(1)}M`;

    if (num >= 1000)
        return `${(num / 1000).toFixed(1)}K`;

    return num;
}