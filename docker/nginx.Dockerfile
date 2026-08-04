# nginx front for the production deployment (built as <app>-web:latest by the
# server's deploy-app webhook, which builds this file automatically when present).
#
# It COPIES public/ out of the already-built app image, so the Vite build output is
# baked in and served by nginx directly -- no shared volume, no start-order
# dependency between the two containers.
#
#   docker build -f docker/nginx.Dockerfile --build-arg APP_IMAGE=tellmolly-com:latest \
#     -t tellmolly-com-web:latest .
ARG APP_IMAGE=tellmolly-com:latest

FROM ${APP_IMAGE} AS app

FROM nginx:1.27-alpine

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /app/public /app/public
