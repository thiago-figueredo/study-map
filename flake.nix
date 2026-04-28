{
  description = "Laravel 12 development environment";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
  };

  outputs = { self, nixpkgs }:
    let
      pkgs = import nixpkgs { system = "x86_64-linux"; };
    in
    {
      devShells.x86_64-linux.default = pkgs.mkShell {
        buildInputs = with pkgs; [
          php83
          php83Extensions.pdo
          php83Extensions.pdo_mysql
          php83Extensions.mbstring
          php83Extensions.xml
          php83Extensions.curl
          php83Extensions.zip
          php83Extensions.bcmath
          php83Extensions.openssl
          php83Extensions.gd
          php83Packages.composer
        ];
      };

      packages.x86_64-linux.php = pkgs.php83;
    };
}