{
  description = "study-map API — Laravel 13 / PHP 8.3 (Pest, Composer)";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
  };

  outputs = { self, nixpkgs }:
    let
      inherit (nixpkgs) lib;
      systems = [
        "aarch64-linux"
        "x86_64-linux"
        "aarch64-darwin"
        "x86_64-darwin"
      ];
      forEachSystem = f: lib.genAttrs systems (system: f nixpkgs.legacyPackages.${system});
    in
    {
      devShells = forEachSystem (pkgs: {
        default = pkgs.mkShell {
          buildInputs = with pkgs; [
            php83
            php83Extensions.pdo
            php83Extensions.pdo_mysql
            php83Extensions.pdo_sqlite
            php83Extensions.mbstring
            php83Extensions.xml
            php83Extensions.curl
            php83Extensions.zip
            php83Extensions.bcmath
            php83Extensions.openssl
            php83Extensions.gd
            php83Extensions.intl
            php83Extensions.fileinfo
            php83Packages.composer
          ];
        };
      });

      packages = forEachSystem (pkgs: {
        php = pkgs.php83;
      });
    };
}
