<template>
    <Layout>
        <template v-slot:content>
            <div v-if="blogDetails" class="blog-container">
                <!-- Full-Screen Image Section -->
                <section class="full-screen-image">
                    <img :src="'/' + blogDetails.cover_image" alt="Cover Image" class="image" />
                    <div class="image-container">
                        <h2>{{ blogDetails.title }}</h2>
                        <p>Published by MyPortfolio Blog - 7 days ago</p>
                        <p>Architectural photography goes beyond capturing buildings...</p>
                    </div>
                </section>

                <!-- Blog Content Section -->
                <section class="blog-content">
                    <p class="blog-text">
                        Photography often emphasizes the rule of thirds as a foundational guideline for composition, helping photographers create balanced and aesthetically pleasing images. However, breaking this rule can lead to more dynamic, thought-provoking, and innovative photographs.
                    </p>
                    <img :src="'/' + blogDetails.image" alt="Photography example" class="blog-image" />

                    <h3 class="blog-subtitle">Understanding the rule of thirds</h3>
                    <p class="blog-text" v-html="blogDetails.content"></p>

                    <h3 class="blog-subtitle">Why break the rule?</h3>
                    <p class="blog-text">
                        Breaking the rule of thirds allows you to:
                    </p>
                    <ul class="blog-list">
                        <li>Create tension or unease for dramatic effect.</li>
                        <li>Draw attention to the center or edges of the frame, emphasizing unconventional focal points.</li>
                        <li>Challenge visual expectations, making your photos stand out in a sea of traditionally composed images.</li>
                    </ul>

                    <h3 class="blog-subtitle">Placing the subject in the center</h3>
                    <p class="blog-text">
                        Centering your subject can evoke a sense of symmetry and balance, particularly in minimalist or graphic compositions. A portrait shot with the subject staring directly into the camera can feel confrontational and powerful.
                    </p>
                    <p class="blog-text">
                        For example, in architectural photography, placing a symmetrical building in the center accentuates its structure and geometry, creating a striking and orderly composition.
                    </p>
                    <img src="/images/covers/covers_3.jpeg" alt="Photography example" class="blog-image" />
                    <h3 class="blog-subtitle">Exploring negative space</h3>
                    <p class="blog-text">
                        By shifting your subject far to the edge of the frame, you can make negative space the focal point of your composition. This approach is particularly effective in minimalist photography or when you want to emphasize isolation, emptiness, or scale.
                    </p>
                    <img src="/images/covers/covers_4.jpeg" alt="Photography example" class="blog-image" />
                    <p class="blog-text">
                        For example, capturing a lone figure on a vast snowy field creates emotional resonance and amplifies the feeling of solitude.
                    </p>
                    <h3 class="blog-subtitle">Dynamic tilts and angles</h3>
                    <p class="blog-text">
                        Tilting the camera to introduce a Dutch angle can create a sense of unease or movement, especially in street photography or action shots. This subtle departure from the norm adds energy and tension to the composition.
                    </p>
                    <p class="blog-text">
                        Consider photographing a skateboarder mid-jump using a tilted frame to emphasize the motion and the dynamic lines of the trajectory.
                    </p>
                    <img src="/images/covers/covers_5.jpeg" alt="Photography example" class="blog-image" />
                    <h3 class="blog-subtitle">Layering for complexity</h3>
                    <p class="blog-text">
                        Breaking the rule of thirds doesn’t mean ignoring composition entirely—it means experimenting with layering elements in the foreground, midground, and background to create visual complexity. This approach draws the viewer’s eye through the frame, uncovering details as they explore.
                    </p>
                    <img src="/images/covers/covers_6.jpeg" alt="Photography example" class="blog-image" />
                    <h3 class="blog-subtitle">Creative examples of rule-breaking</h3>
                    <ul class="blog-list">
                        <li><strong>Portraits:</strong> Center the subject to create symmetry or exaggerate asymmetry by placing them far to one side.</li>
                        <li><strong>Landscapes:</strong> Position the horizon line at the very top or bottom of the frame to emphasize the sky or the foreground.</li>
                        <li><strong>Abstracts:</strong> Place textures, patterns, or reflections arbitrarily within the frame to challenge the viewer’s perception.</li>
                    </ul>
                    <img src="/images/covers/covers_7.jpeg" alt="Photography example" class="blog-image" />
                    <h3 class="blog-subtitle">Practical tips for breaking the rule of thirds</h3>
                    <ul class="blog-list">
                        <li><strong>Experiment first, edit later:</strong> Capture multiple versions of a scene using different compositions. Compare the standard rule of thirds shot with more experimental approaches to see which one resonates.</li>
                        <li><strong>Start simple:</strong> Gradually introduce rule-breaking elements into your work. For instance, experiment with a centered subject or exaggerated negative space before venturing into more complex compositions.</li>
                        <li><strong>Observe and analyze:</strong> Study images from master photographers who regularly break the rule of thirds, such as Henri Cartier-Bresson or Cindy Sherman, to understand how they use unconventional composition to create impact.</li>
                    </ul>
                    <img src="/images/covers/covers_8.jpeg" alt="Photography example" class="blog-image" />
                    <p class="blog-text">
                        Breaking the rule of thirds is not about rejecting composition principles altogether but rather using them as a springboard for creativity. By understanding when and how to break the rules, you can create images that are not only visually striking but also uniquely yours. Push boundaries, take risks, and embrace the unexpected—your photography will be better for it.
                    </p>
                </section>
            </div>
        </template>
    </Layout>
</template>


<script>
import axios from 'axios';
import { notification } from 'ant-design-vue';
import Layout from './Layout.vue';
import getUrlList from '../provider.js';
export default {
    name: "BlogDetails",
    components: {
        Layout,
    },
    data() {
        return {
            blogDetails: null, // Dữ liệu blog sẽ lưu ở đây
        };
    },
    async created() {
        const slug = this.$route.params.slug; // Lấy slug từ URL
        await this.fetchBlogDetails(slug);  // Gọi API để lấy dữ liệu blog
    },
    methods: {
        async fetchBlogDetails(slug) {
            try {
                const response = await axios.get(getUrlList().getBlogDetails(slug));
                this.blogDetails = response.data.blog;
            } catch (error) {
                console.error("Lỗi khi lấy dữ liệu blog:", error);
            }
        },
    },
};
</script>

<style scoped>
.blog-container {
    max-width: 2200px;
    margin: auto;
}

.full-screen-image {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: rgba(0, 0, 0, 0.5);
    text-align: center;
}

.blog-content {
    max-width: 800px;
    margin: 50px auto;
    text-align: left;
}

.blog-text {
    font-size: 18px;
    line-height: 1.6;
    color: #333;
    margin-bottom: 20px;
    text-align: justify;
    text-justify: inter-word;
}

.blog-image {
    width: 100%;
    max-width: 900px;
    height: auto;
    display: block;
    margin: 20px auto;
    border-radius: 10px;
}

.blog-subtitle {
    font-size: 24px;
    font-weight: bold;
    margin-top: 15px;
    color: #222;
}

.blog-list {
    font-size: 18px;
    line-height: 1.6;
    color: #333;
    padding-left: 20px;
}

.blog-list li {
    margin-bottom: 10px;
}

</style>
