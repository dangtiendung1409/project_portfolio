<template>
    <div class="app-bar">
        <div class="app-bar-content">
            <div
                class="app-bar-item"
                :class="{ active: activeItem === 'following', selected: activeItem === 'following' }"
                @click="setActive('following')"
            >
                Following
            </div>
            <div
                class="app-bar-item"
                :class="{ active: activeItem === 'forYou', selected: activeItem === 'forYou' }"
                @click="setActive('forYou')"
            >
                For You
            </div>
            <div
                class="app-bar-item"
                :class="{ active: activeItem === 'explore', selected: activeItem === 'explore' }"
                @click="setActive('explore')"
            >
                Explore
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        activeItem: String,
        setActive: Function,
    },
};
</script>
