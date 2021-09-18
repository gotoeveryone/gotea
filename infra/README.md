# リリースサーバセットアップ

[Ansible](https://www.ansible.com/) を使ってサーバのセットアップを行います。

### Requirements

- Python 3.9
- pipenv
- サーバの秘密鍵を持っており、サーバへ SSH で接続できること

### Setup

```console
$ cd {this_directory}
$ pipenv install
```

### Run

```console
$ pipenv run setup --private-key={private key path}
```
