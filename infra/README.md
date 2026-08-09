# リリースサーバセットアップ

[Ansible](https://www.ansible.com/) を使ってサーバのセットアップを行います。

### Requirements

- Python 3.12
- uv
- サーバの秘密鍵を持っており、サーバへ SSH で接続できること

### Setup

```console
$ cd {this_directory}
$ uv sync
```

### Run

```console
$ uv run ansible-playbook setup.yml -i hosts/server \
    --private-key={private key path}
```

Lint を実行する場合は、次のコマンドを使用します。

```console
$ uv run ansible-lint ./
```
