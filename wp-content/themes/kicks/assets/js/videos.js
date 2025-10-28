import { qa } from './dom';
import { debounce } from './utilities';

const selectVideoSource = (video) => {
  if (video.tagName !== 'VIDEO') {
    return;
  }
  const { currentTime, parentElement } = video;
  const { offsetWidth, offsetHeight } = parentElement;
  const {
    srcSm: sm, srcMd: md, srcLg: lg, portrait,
  } = video.dataset;

  let source = null;

  const isPortrait = offsetHeight > offsetWidth;

  if (offsetWidth < 768) {
    source = sm;
  } else if ((portrait && isPortrait) || (!portrait && offsetWidth >= 768 && offsetWidth <= 1280)) {
    source = md;
  } else {
    source = lg;
  }

  if (video.getAttribute('src') === source) {
    return;
  }
  video.setAttribute('src', source);
  video.currentTime = currentTime;
};

const playVideo = (video) => {
  const promise = video.play();
  video.setAttribute('data-playing', true);
  if (promise !== undefined) {
    promise.catch((error) => {
      if (error.name === 'NotAllowedError') {
        // Apple devices don't play videos in low power mode and show a play button instead.
        video.remove();
      }
    });
  }
};

export default function initVideos() {
  const videos = qa('[data-video]');

  videos.forEach((video) => {
    selectVideoSource(video);
    video.on('playing', () => playVideo(video));
    video.on('canplay', () => playVideo(video));
  });
  window.on('resize', debounce(() => {
    videos.forEach((video) => {
      selectVideoSource(video);
    });
  }));
}
