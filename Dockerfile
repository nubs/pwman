FROM nubs/phpunit

USER root

RUN pacman --sync --refresh --noconfirm --noprogressbar --quiet && pacman --sync --noconfirm --noprogressbar --quiet gpgme base-devel

# Get around a bug with docker registry where the owner is root for the home
# user.  See https://github.com/docker/docker/issues/5892.
RUN chown -R build /home/build

USER build

RUN curl -sS https://aur.archlinux.org/packages/ph/php-gnupg/php-gnupg.tar.gz | tar -xzC $HOME
RUN cd $HOME/php-gnupg && makepkg --clean --noconfirm --noprogressbar

USER root

RUN pacman --upgrade --noconfirm --noprogressbar $HOME/php-gnupg/php-gnupg*.pkg.tar.xz

USER build
